<?php

namespace App\Services;

use Google_Client;
use Google_Service_YouTube;

class YoutubeClient
{
    public $client;

    public function __construct($apiKey)
    {
        $client = new Google_Client();
        $client->setDeveloperKey($apiKey);
        $this->client = new Google_Service_YouTube($client);
    }

    public function getVideoStats(array $videoIds)
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