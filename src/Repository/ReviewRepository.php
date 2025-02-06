<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }
    public function getReviewCountsByProduct(): array
    {
        return $this->createQueryBuilder('r')
            ->select('p.name as productName, COUNT(r.id) as reviewCount')
            ->join('r.product', 'p')
            ->groupBy('p.id')
            ->orderBy('reviewCount', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
