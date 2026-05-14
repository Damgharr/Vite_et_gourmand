<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee')]
#[IsGranted('ROLE_EMPLOYEE')]
class EmployeeOrderController extends AbstractController
{
    #[Route('/orders', name: 'employee_orders')]
    public function index(OrderRepository $orderRepository, Request $request): Response
    {
        $qb = $orderRepository->createQueryBuilder('o')
            ->leftJoin('o.user', 'u')
            ->addSelect('u')
            ->leftJoin('o.menu', 'm')
            ->addSelect('m')
            ->where('o.status != :finished')
            ->setParameter('finished', 'terminée')
            ->orderBy('o.orderDate', 'DESC');

        if ($request->query->get('status')) {
            $qb->andWhere('o.status = :status')->setParameter('status', $request->query->get('status'));
        }
        if ($request->query->get('client')) {
            $qb->andWhere('LOWER(u.firstName) LIKE :client OR LOWER(u.lastName) LIKE :client OR LOWER(u.email) LIKE :client')
                ->setParameter('client', '%' . strtolower($request->query->get('client')) . '%');
        }

        return $this->render('employee/orders/index.html.twig', [
            'orders' => $qb->getQuery()->getResult(),
            'statuses' => ['en attente', 'en préparation', 'en livraison', 'en attente du retour de matériel', 'terminée', 'annulée'],
        ]);
    }

    #[Route('/orders/{id}/status', name: 'employee_orders_status', methods: ['POST'])]
    public function status(Order $order, Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $oldStatus = $order->getStatus();
        $newStatus = $request->request->get('status');
        $order->setStatus($newStatus);
        $em->flush();

        if ($newStatus === 'en attente du retour de matériel' && $oldStatus !== $newStatus) {
            $email = (new TemplatedEmail())
                ->to($order->getUser()->getEmail())
                ->subject('Retour de matériel')
                ->htmlTemplate('emails/equipment_return.html.twig')
                ->context(['order' => $order, 'user' => $order->getUser()]);
            $mailer->send($email);
        }

        $this->addFlash('success', 'Statut mis à jour.');
        return $this->redirectToRoute('employee_orders');
    }

    #[Route('/orders/{id}/cancel', name: 'employee_orders_cancel', methods: ['POST'])]
    public function cancel(Order $order, Request $request, EntityManagerInterface $em): Response
    {
        $order->setStatus('annulée');
        $em->flush();
        $this->addFlash('success', 'Commande annulée. Contact : ' . $request->request->get('contactMethod') . '. Motif : ' . $request->request->get('reason'));
        return $this->redirectToRoute('employee_orders');
    }
}
