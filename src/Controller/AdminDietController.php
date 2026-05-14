<?php

namespace App\Controller;

use App\Entity\Diet;
use App\Repository\DietRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDietController extends AbstractController
{
    #[Route('/diets', name: 'admin_diets')]
    public function index(DietRepository $dietRepository): Response
    {
        return $this->render('admin/diets/index.html.twig', [
            'diets' => $dietRepository->findAll(),
        ]);
    }

    #[Route('/diets/new', name: 'admin_diets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $diet = new Diet();
            $diet->setLabel($request->request->get('label'));
            $em->persist($diet);
            $em->flush();
            $this->addFlash('success', 'Régime créé.');
            return $this->redirectToRoute('admin_diets');
        }
        return $this->render('admin/diets/new.html.twig');
    }

    #[Route('/diets/{id}/edit', name: 'admin_diets_edit', methods: ['GET', 'POST'])]
    public function edit(Diet $diet, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $diet->setLabel($request->request->get('label'));
            $em->flush();
            $this->addFlash('success', 'Régime modifié.');
            return $this->redirectToRoute('admin_diets');
        }
        return $this->render('admin/diets/edit.html.twig', ['diet' => $diet]);
    }

    #[Route('/diets/{id}/delete', name: 'admin_diets_delete', methods: ['POST'])]
    public function delete(Diet $diet, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $diet->getId(), $request->request->get('_token'))) {
            $em->remove($diet);
            $em->flush();
            $this->addFlash('success', 'Régime supprimé.');
        }
        return $this->redirectToRoute('admin_diets');
    }
}
