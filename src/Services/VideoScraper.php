<?php

namespace App\Services;

use App\Entity\Channel;
use Doctrine\ORM\EntityManagerInterface;

class VideoScraper
{
    private $client;
    private $em;

    public function __construct(YoutubeClient $client, EntityManagerInterface $em)
    {
        $this->client = $client->client;
        $this->em = $em;
    }

    public function getVideos(string $channelId, ?string $pageToken = null)
    {
        $params = [
            'channelId' => $channelId,
            'maxResults' => 50,
            'type' => 'video',
            'order' => 'date'
        ];

        if ($pageToken) {
            $params['pageToken'] = $pageToken;
        }

        $response = $this->client->search->listSearch(
            'snippet',
            $params
        );

        return $response;
    }

    public function getChannel(string $channelId): Channel
    {
        $channel = $this->em->getRepository(Channel::class)->findOneByChannelId($channelId);

        if (!$channel) {
            $channel = new Channel;

            $response = $this->client->channels->listChannels(
                'snippet',
                [
                    'id' => $channelId
                ]
            );
            $title = $response->getItems()[0]->getSnippet()->getTitle();

            $channel->setChannelId($channelId)
                ->setName($title);
            $this->em->persist($channel);
            $this->em->flush();
        }
        return $channel;
    }
}