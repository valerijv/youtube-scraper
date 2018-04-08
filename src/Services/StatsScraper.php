<?php

namespace App\Services;

use App\Entity\Stats;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

class StatsScraper
{
    private $client;
    private $em;

    public function __construct(YoutubeClient $client, EntityManagerInterface $em)
    {
        $this->client = $client->client;
        $this->em = $em;
    }

    public function getStats(array $videos, bool $tags = false): void
    {
        $videoIds = [];
        foreach ($videos as $video) {
            $videoIds[$video->getVideoId()] = $video;
        }

        foreach ($this->getVideoData(array_keys($videoIds)) as $item) {
            $video = $videoIds[$item->getId()];

            $stats = new Stats;
            $stats->setVideo($video)
                ->setViewCount($item->getStatistics()->getViewCount())
                ->setLikeCount($item->getStatistics()->getLikeCount())
                ->setDislikeCount($item->getStatistics()->getDislikeCount())
                ->setCommentCount($item->getStatistics()->getCommentCount())
                ->setFavoriteCount($item->getStatistics()->getFavoriteCount());
            $this->em->persist($stats);

            if ($tags) {
                $itemTags = $item->getSnippet()->getTags() ?? [];
                foreach ($itemTags as $value) {
                    $tag = $this->em->getRepository(Tag::class)->findOneByName($value);
                    if (!$tag) {
                        $tag = new Tag;
                    }
                    $tag->setName($value)
                        ->addVideo($video);
                    $this->em->persist($tag);
                }
            }
        }
    }

    public function getVideoData(array $videoIds)
    {
        $response = $this->client->videos->listVideos(
            'statistics,snippet',
            [
                'id' => implode(',', $videoIds),
            ]
        );

        return $response->getItems();
    }
}