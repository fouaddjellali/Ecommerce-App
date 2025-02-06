<?php
namespace App\Controller;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(
        UserRepository $userRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        ReviewRepository $reviewRepository
    ): Response {
        $totalUsers = $userRepository->count([]);
        $totalProducts = $productRepository->count([]);
        $totalOrders = $orderRepository->count([]);
        $totalReviews = $reviewRepository->count([]);
        $userGrowth = $userRepository->getUserGrowthByMonth();
        $orderStatusCounts = $orderRepository->getOrdersByStatus();
        $productsByCategory = $productRepository->getProductsByCategory();
        $monthlyRevenues = $orderRepository->getMonthlyRevenues();
        $reviewCounts = $reviewRepository->getReviewCountsByProduct();
        return $this->render('dashboard/index.html.twig', [
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalReviews' => $totalReviews,
            'userGrowth' => json_encode($userGrowth),
            'orderStatusCounts' => json_encode($orderStatusCounts),
            'productsByCategory' => json_encode($productsByCategory),
            'monthlyRevenues' => json_encode($monthlyRevenues),
            'reviewCounts' => json_encode($reviewCounts),
        ]);
    }
}
