<?php

namespace App\Controller;

use App\Entity\Stats;
use App\Entity\Video;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    private $serializer;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }
    /**
     * @Route("/videos", name="videos")
     */
    public function videos()
    {
        $videos = $this->getDoctrine()->getRepository(Video::class)
            ->findAll();

        $channelMedians = [];

        foreach ($videos as $video) {
            $channelId = $video->getChannel()->getId();

            // some caching
            if (!isset($channelMedians[$channelId])) {
                $channelMedians[$channelId] = $this->getDoctrine()->getRepository(Stats::class)
                    ->getChannelMedian($video->getChannel()->getId());
            }

            $videoMedian = $this->getDoctrine()->getRepository(Stats::class)
                ->getVideoMedian($video->getId());
            
            $score = round($videoMedian / $channelMedians[$channelId], 3);

            $video->setScore($score);
        }

        return $this->json(
            $this->serializer->toArray($videos)
        );
    }
}
