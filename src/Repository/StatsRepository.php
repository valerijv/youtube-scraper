<?php

namespace App\Repository;

use App\Entity\Stats;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class StatsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Stats::class);
    }

    public function getChannelMedian(int $channelId): ?int
    {
        $conn = $this->getEntityManager()->getConnection();

        // select first stats date
        $sql = '
            SELECT s.created_at FROM stats s
            left join video v on v.id = s.video_id
            where v.channel_id = :channel_id
            order by s.created_at asc;
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['channel_id' => $channelId]);
        $date = $stmt->fetch()['created_at'];
        $date = Carbon::parse($date)->addHour()->format('Y-m-d H:i:s');

        $sql = '
            SELECT floor(count(s.id) / 2) as num FROM stats s
            left join video v on v.id = s.video_id
            where v.channel_id = :channel_id
            and s.created_at < :date
            order by view_count;
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'channel_id' => $channelId,
            'date' => $date
        ]);

        $medianRow = $stmt->fetch()['num'];

        $sql = '
            SELECT view_count FROM stats s
            left join video v on v.id = s.video_id
            where v.channel_id = :channel_id
            and s.created_at < :date
            order by view_count
            limit ' . $medianRow . ', 1
        ';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'channel_id' => $channelId,
            'date' => $date
        ]);

        $median = $stmt->fetch()['view_count'];
        
        return $median;
    }

    public function getVideoMedian(int $videoId): ? int
    {
        $conn = $this->getEntityManager()->getConnection();

        // select first stats date
        $sql = '
            SELECT created_at FROM stats
            where video_id = :video_id
            order by created_at asc;
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['video_id' => $videoId]);
        $date = $stmt->fetch()['created_at'];
        $date = Carbon::parse($date)->addHour()->format('Y-m-d H:i:s');
        
        // select median row number
        $sql = '
            SELECT floor(count(id) / 2) as num FROM stats
            where video_id = :video_id
            and created_at < :date
            order by view_count;
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'video_id' => $videoId,
            'date' => $date
        ]);
        $medianRow = $stmt->fetch()['num'];

        // get median
        $sql = '
            SELECT view_count FROM stats
            where video_id = :video_id
            and created_at < :date
            order by view_count;
            limit ' . $medianRow . ', 1
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'video_id' => $videoId,
            'date' => $date
        ]);
        $median = $stmt->fetch()['view_count'];

        return $median;
    }
}
