<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminEmployeeController extends AbstractController
{
    #[Route('/employees', name: 'admin_employees')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('backoffice/employees/index.html.twig', [
            'employees' => $userRepository->findByRole('ROLE_EMPLOYEE'),
        ]);
    }

    #[Route('/employees/new', name: 'admin_employees_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        if ($request->isMethod('POST')) {
            $user = new User();
            $user->setEmail($request->request->get('email'));
            $user->setFirstName($request->request->get('firstName'));
            $user->setLastName($request->request->get('lastName'));
            $user->setPhone($request->request->get('phone'));
            $user->setRoles(['ROLE_EMPLOYEE']);
            $user->setPassword($hasher->hashPassword($user, $request->request->get('password')));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Employé créé.');
            return $this->redirectToRoute('admin_employees');
        }
        return $this->render('backoffice/employees/new.html.twig');
    }

    #[Route('/employees/{id}/toggle', name: 'admin_employees_toggle', methods: ['POST'])]
    public function toggle(User $user, EntityManagerInterface $em): Response
    {
        $user->setEnabled(!$user->isEnabled());
        $em->flush();
        $this->addFlash('success', 'Statut mis à jour.');
        return $this->redirectToRoute('admin_employees');
    }
}
