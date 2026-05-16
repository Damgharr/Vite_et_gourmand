<?php

namespace App\Controller;

use App\Entity\Allergen;
use App\Repository\AllergenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminAllergenController extends AbstractController
{
    #[Route('/allergens', name: 'admin_allergens')]
    public function index(AllergenRepository $allergenRepository): Response
    {
        return $this->render('backoffice/allergens/index.html.twig', [
            'allergens' => $allergenRepository->findAll(),
        ]);
    }

    #[Route('/allergens/new', name: 'admin_allergens_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $allergen = new Allergen();
            $allergen->setLabel($request->request->get('label'));
            $em->persist($allergen);
            $em->flush();
            $this->addFlash('success', 'Allergène créé.');
            return $this->redirectToRoute('admin_allergens');
        }
        return $this->render('backoffice/allergens/new.html.twig');
    }

    #[Route('/allergens/{id}/edit', name: 'admin_allergens_edit', methods: ['GET', 'POST'])]
    public function edit(Allergen $allergen, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $allergen->setLabel($request->request->get('label'));
            $em->flush();
            $this->addFlash('success', 'Allergène modifié.');
            return $this->redirectToRoute('admin_allergens');
        }
        return $this->render('backoffice/allergens/edit.html.twig', ['allergen' => $allergen]);
    }

    #[Route('/allergens/{id}/delete', name: 'admin_allergens_delete', methods: ['POST'])]
    public function delete(Allergen $allergen, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $allergen->getId(), $request->request->get('_token'))) {
            $em->remove($allergen);
            $em->flush();
            $this->addFlash('success', 'Allergène supprimé.');
        }
        return $this->redirectToRoute('admin_allergens');
    }
}
