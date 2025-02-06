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
    public function getMonthlyRevenues(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        SELECT 
            MONTH(o.created_at) AS month, 
            SUM(oi.price * oi.quantity) AS revenue 
        FROM orders o
        INNER JOIN order_items oi ON o.id = oi.order_id
     
        GROUP BY month
        ORDER BY month ASC
    ";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
    }




}
