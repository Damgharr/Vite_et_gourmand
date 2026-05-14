<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Repository\AllergenRepository;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDishController extends AbstractController
{
    #[Route('/dishes', name: 'admin_dishes')]
    public function index(DishRepository $dishRepository): Response
    {
        return $this->render('admin/dishes/index.html.twig', [
            'dishes' => $dishRepository->findAll(),
        ]);
    }

    #[Route('/dishes/new', name: 'admin_dishes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, AllergenRepository $allergenRepository): Response
    {
        if ($request->isMethod('POST')) {
            $dish = new Dish();
            $dish->setTitle($request->request->get('title'));
            $dish->setDescription($request->request->get('description'));
            $photo = $request->files->get('photo');
            if ($photo) {
                $dish->setPhoto(file_get_contents($photo->getPathname()));
            }
            foreach ($request->request->all()['allergens'] ?? [] as $allergenId) {
                $dish->addAllergen($allergenRepository->find($allergenId));
            }
            $em->persist($dish);
            $em->flush();
            $this->addFlash('success', 'Plat créé.');
            return $this->redirectToRoute('admin_dishes');
        }
        return $this->render('admin/dishes/new.html.twig', [
            'allergens' => $allergenRepository->findAll(),
        ]);
    }

    #[Route('/dishes/{id}/edit', name: 'admin_dishes_edit', methods: ['GET', 'POST'])]
    public function edit(Dish $dish, Request $request, EntityManagerInterface $em, AllergenRepository $allergenRepository): Response
    {
        if ($request->isMethod('POST')) {
            $dish->setTitle($request->request->get('title'));
            $dish->setDescription($request->request->get('description'));
            $photo = $request->files->get('photo');
            if ($photo) {
                $dish->setPhoto(file_get_contents($photo->getPathname()));
            }
            foreach ($dish->getAllergens() as $allergen) {
                $dish->removeAllergen($allergen);
            }
            foreach ($request->request->all()['allergens'] ?? [] as $allergenId) {
                $dish->addAllergen($allergenRepository->find($allergenId));
            }
            $em->flush();
            $this->addFlash('success', 'Plat modifié.');
            return $this->redirectToRoute('admin_dishes');
        }
        return $this->render('admin/dishes/edit.html.twig', [
            'dish' => $dish,
            'allergens' => $allergenRepository->findAll(),
        ]);
    }

    #[Route('/dishes/{id}/delete', name: 'admin_dishes_delete', methods: ['POST'])]
    public function delete(Dish $dish, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dish->getId(), $request->request->get('_token'))) {
            $em->remove($dish);
            $em->flush();
            $this->addFlash('success', 'Plat supprimé.');
        }
        return $this->redirectToRoute('admin_dishes');
    }
}
