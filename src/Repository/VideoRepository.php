<?php

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class VideoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function findGreaterThanId(Channel $channel, int $id = 0, int $limit = 50): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.channel = :channel')
            ->andWhere('v.id > :id')
            ->setParameter('channel', $channel)
            ->setParameter('id', $id)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getCountByChannel($channel): int
    {
        return $this->createQueryBuilder('v')
            ->select('count(v.id)')
            ->andWhere('v.channel = :channel')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    public function findByTag($tag)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            select video.* from 
            SELECT s.created_at FROM stats s
            left join video v on v.id = s.video_id
            where v.channel_id = :channel_id
            order by s.created_at asc;
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['channel_id' => $channelId]);

        return $stmt->fetchAll();

    }
}
