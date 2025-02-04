<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
    public function getProductsByCategory(): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('c.name AS category, COUNT(p.id) AS count')
            ->join('p.category', 'c')
            ->groupBy('c.name')
            ->getQuery();
        return $qb->getResult();
    }
}
