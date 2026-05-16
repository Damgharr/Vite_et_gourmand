<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\DietRepository;
use App\Repository\DishRepository;
use App\Repository\MenuRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee')]
#[IsGranted('ROLE_EMPLOYEE')]
class MenuController extends AbstractController
{
    #[Route('/menus', name: 'employee_menus')]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('backoffice/menus/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    #[Route('/menus/new', name: 'employee_menus_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, ThemeRepository $themeRepository, DietRepository $dietRepository, DishRepository $dishRepository): Response
    {
        if ($request->isMethod('POST')) {
            $menu = new Menu();
            $menu->setTitle($request->request->get('title'));
            $menu->setDescription($request->request->get('description'));
            $menu->setMinPeopleAmount((int) $request->request->get('minPeopleAmount'));
            $menu->setPricePerPeople($request->request->get('pricePerPeople'));
            $menu->setRemaining((int) $request->request->get('remaining'));
            $menu->setTheme($themeRepository->find($request->request->get('theme')));
            $menu->setDiet($dietRepository->find($request->request->get('diet')));
            foreach ($request->request->all()['dishes'] ?? [] as $dishId) {
                $menu->addDish($dishRepository->find($dishId));
            }
            $em->persist($menu);
            $em->flush();
            $this->addFlash('success', 'Menu créé.');
            return $this->redirectToRoute('employee_menus');
        }
        return $this->render('backoffice/menus/new.html.twig', [
            'themes' => $themeRepository->findAll(),
            'diets' => $dietRepository->findAll(),
            'dishes' => $dishRepository->findAll(),
        ]);
    }

    #[Route('/menus/{id}/edit', name: 'employee_menus_edit', methods: ['GET', 'POST'])]
    public function edit(Menu $menu, Request $request, EntityManagerInterface $em, ThemeRepository $themeRepository, DietRepository $dietRepository, DishRepository $dishRepository): Response
    {
        if ($request->isMethod('POST')) {
            $menu->setTitle($request->request->get('title'));
            $menu->setDescription($request->request->get('description'));
            $menu->setMinPeopleAmount((int) $request->request->get('minPeopleAmount'));
            $menu->setPricePerPeople($request->request->get('pricePerPeople'));
            $menu->setRemaining((int) $request->request->get('remaining'));
            $menu->setTheme($themeRepository->find($request->request->get('theme')));
            $menu->setDiet($dietRepository->find($request->request->get('diet')));
            foreach ($menu->getDishes() as $dish) {
                $menu->removeDish($dish);
            }
            foreach ($request->request->all()['dishes'] ?? [] as $dishId) {
                $menu->addDish($dishRepository->find($dishId));
            }
            $em->flush();
            $this->addFlash('success', 'Menu modifié.');
            return $this->redirectToRoute('employee_menus');
        }
        return $this->render('backoffice/menus/edit.html.twig', [
            'menu' => $menu,
            'themes' => $themeRepository->findAll(),
            'diets' => $dietRepository->findAll(),
            'dishes' => $dishRepository->findAll(),
        ]);
    }

    #[Route('/menus/{id}/delete', name: 'employee_menus_delete', methods: ['POST'])]
    public function delete(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $menu->getId(), $request->request->get('_token'))) {
            $em->remove($menu);
            $em->flush();
            $this->addFlash('success', 'Menu supprimé.');
        }
        return $this->redirectToRoute('employee_menus');
    }
}
