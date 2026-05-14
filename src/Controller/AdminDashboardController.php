<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    #[Route('/dashboard/data', name: 'admin_dashboard_data')]
    public function data(Request $request, OrderRepository $orderRepository): JsonResponse
    {
        $from = $request->query->get('from') ? new \DateTime($request->query->get('from')) : (new \DateTime())->modify('-30 days');
        $to = $request->query->get('to') ? new \DateTime($request->query->get('to')) : new \DateTime();
        $period = $request->query->get('period', 'day');
        $menuId = $request->query->get('menu');

        $orders = $orderRepository->createQueryBuilder('o')
            ->where('o.orderDate >= :from')
            ->andWhere('o.orderDate <= :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('o.orderDate', 'ASC');

        if ($menuId) {
            $orders->andWhere('o.menu = :menu')->setParameter('menu', $menuId);
        }

        $results = $orders->getQuery()->getResult();
        $grouped = [];

        foreach ($results as $order) {
            $date = $order->getOrderDate();
            $key = match ($period) {
                'week' => $date->format('o-\WW'),
                'month' => $date->format('Y-m'),
                default => $date->format('Y-m-d'),
            };
            if (!isset($grouped[$key])) {
                $grouped[$key] = ['revenue' => 0, 'orders' => 0];
            }
            $revenue = ((float) $order->getMenuPrice() * $order->getPeopleAmount()) + (float) $order->getDeliveryPrice();
            $grouped[$key]['revenue'] += $revenue;
            $grouped[$key]['orders'] += 1;
        }

        $labels = array_keys($grouped);
        $revenue = array_map(fn($v) => round($v['revenue'], 2), array_values($grouped));
        $orders = array_map(fn($v) => $v['orders'], array_values($grouped));

        return $this->json(['labels' => $labels, 'revenue' => $revenue, 'orders' => $orders]);
    }
}
