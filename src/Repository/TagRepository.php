<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TagRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function findByQuery(string $query)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name like :query')
            ->setParameter('query', $query . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
