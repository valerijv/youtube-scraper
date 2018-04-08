<?php

namespace App\Command;

use App\Entity\Channel;
use App\Entity\Video;
use App\Services\StatsScraper;
use App\Services\VideoScraper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppGetStatsCommand extends Command
{
    protected static $defaultName = 'app:get-stats';
    private $statsScraper;
    private $videoScraper;
    private $io;
    private $totalCount = 0;
    private $currentCount = 0;
    private $delay = 1; // delay for scraper

    public function __construct(VideoScraper $videoScraper, StatsScraper $statsScraper, EntityManagerInterface $em)
    {
        $this->statsScraper = $statsScraper;
        $this->em = $em;
        parent::__construct();
        $this->videoScraper = $videoScraper;
    }

    protected function configure()
    {
        $this
            ->setDescription('Gets video statistics for existing videos in our database')
            ->addArgument('channel_id', InputArgument::REQUIRED, 'Youtube channel ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $channelId = $input->getArgument('channel_id');

        $channel = $this->em->getRepository(Channel::class)
                ->findOneByChannelId($channelId);

        if (!$channel) {
            return $this->io->error("No channel found with such ID");
        }

        $this->totalCount = $this->em->getRepository(Video::class)
            ->getCountByChannel($channel);

        $this->io->note("Starting scraping. Found $this->totalCount videos in the channel");

        $this->parse($channel);

        return $this->io->success("Finished crawling. All stats channel updated.");
    }

    private function parse(Channel $channel, int $videoId = 0)
    {
        $videos = $this->em->getRepository(Video::class)
            ->findGreaterThanId($channel, $videoId);

        $this->currentCount += count($videos);

        if ($videos) {
            $this->io->note("Scraping videos $this->currentCount of $this->totalCount");
            $this->statsScraper->getStats($videos);
            $this->em->flush();
            $lastVideo = $videos[count($videos) - 1];
            sleep($this->delay);
            $this->parse($channel, $lastVideo->getId());
        }
    }

}
