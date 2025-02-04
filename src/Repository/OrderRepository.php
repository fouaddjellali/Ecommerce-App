<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }
    public function getOrdersByStatus(): array
    {
        $qb = $this->createQueryBuilder('o')
            ->select('o.status, COUNT(o.id) AS count')
            ->groupBy('o.status')
            ->getQuery();

        return $qb->getResult();
    }

}
