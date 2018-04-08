<?php

namespace App\Command;

use App\Entity\Video;
use App\Services\StatsScraper;
use App\Services\VideoScraper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Carbon\Carbon;

class AppGetVideosCommand extends Command
{
    protected static $defaultName = 'app:get-videos';
    private $videoScraper;
    private $statsScraper;
    private $io;
    private $videoCount;
    private $em;
    private $delay = 1; // delay for scraper

    public function __construct(VideoScraper $videoScraper, StatsScraper $statsScraper, EntityManagerInterface $em)
    {
        $this->videoScraper = $videoScraper;
        $this->em = $em;
        parent::__construct();
        $this->statsScraper = $statsScraper;
    }

    protected function configure()
    {
        $this
            ->setDescription('Gets new videos (with stats and tags) from a specific Youtube channel')
            ->addArgument('channel_id', InputArgument::REQUIRED, 'Youtube channel ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $channelId = $input->getArgument('channel_id');

        $this->videoCount = 0;
        $this->parse($channelId);
    }

    private function parse(string $channelId, ?string $pageToken = null)
    {
        if ($pageToken) {
            $this->io->note("Crawling channel: $channelId, pageToken: " . $pageToken);
        } else {
            $this->io->note("Crawling channel: $channelId, page 1");
        }

        $channel = $this->videoScraper->getChannel($channelId);
        $response = $this->videoScraper->getVideos($channelId, $pageToken);

        if (!$pageToken) {
            $totalResults = $response->getPageInfo()->getTotalResults();
            $this->io->note("Total videos on this channel: $totalResults");
            $diff = Carbon::now()->addSeconds(round($totalResults / 20))->diffForHumans();
            $this->io->note("Estimated crawl time: $diff");
        }

        $videos = [];
        foreach ($response->getItems() as $item) {
            $videoId = $item->getId()->getVideoId();

            $video = $this->em->getRepository(Video::class)->findOneByVideoId($videoId);
            if (!$video) {
                $videoData = $item->getSnippet();
                $video = new Video;
                $video->setChannel($channel)
                    ->setVideoId($videoId)
                    ->setTitle($videoData->getTitle())
                    ->setDescription($videoData->getDescription())
                    ->setPublishedAt(new \DateTime($videoData->getPublishedAt()));
                $this->em->persist($video);

                $videos[] = $video;
                $this->videoCount++;
            } else {
                $this->getStats($videos);
                $this->em->flush();
                return $this->io->success("Stopped crawling: Video already exists. Found new $this->videoCount videos");
            }
        }
        $this->getStats($videos);
        $this->em->flush();

        sleep($this->delay);
        if ($response->getNextPageToken()) {
            // crawl next page
            return $this->parse($channelId, $response->getNextPageToken());
        }
        return $this->io->success("Finished crawling. Inserted $this->videoCount videos");
    }

    private function getStats(array $videos): void
    {
        if (!empty($videos)) {
            $this->io->note("Getting statistics");
            sleep($this->delay);
            $this->statsScraper->getStats($videos, true);
        }
    }
}
