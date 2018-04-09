<?php

namespace App\Controller;

use App\Entity\Stats;
use App\Entity\Tag;
use App\Entity\Video;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Request;
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
    public function videos(Request $request)
    {
        if (is_numeric($request->get('tag'))) {
            $tag = $this->getDoctrine()->getRepository(Tag::class)->find($request->get('tag'));
            $videos = $tag->getVideos();
        } else {
            $videos = $this->getDoctrine()->getRepository(Video::class)
                ->findBy([], [], 1000);
        }

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

    /**
     * @Route("/tags", name="tags")
     */
    public function tags(Request $request)
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)
            ->findByQuery($request->get('query', 'a'));

        return $this->json(
            $this->serializer->toArray($tags)
        );
    }
}
