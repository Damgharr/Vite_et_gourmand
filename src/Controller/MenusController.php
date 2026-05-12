<?php

namespace App\Controller;

use App\Repository\DietRepository;
use App\Repository\MenuRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenusController extends AbstractController
{
    #[Route('/menus', name: 'menus')]
    public function index(Request $request, MenuRepository $menuRepository, ThemeRepository $themeRepository, DietRepository $dietRepository): Response
    {
        $filters = [
            'minPrice' => $request->query->get('minPrice', 0),
            'maxPrice' => $request->query->get('maxPrice', 1000),
            'theme' => $request->query->get('theme'),
            'diet' => $request->query->get('diet'),
            'minPeople' => $request->query->get('minPeople'),
        ];

        $menus = $menuRepository->findFiltered($filters);
        $themes = $themeRepository->findAll();
        $diets = $dietRepository->findAll();

        return $this->render('pages/menus.html.twig', [
            'menus' => array_slice($menus, 0, 6),
            'themes' => $themes,
            'diets' => $diets,
            'filters' => $filters,
            'hasMore' => count($menus) > 6,
        ]);
    }

    #[Route('/menus/more', name: 'menus_more')]
    public function loadMore(Request $request, MenuRepository $menuRepository): JsonResponse
    {
        $filters = [
            'minPrice' => $request->query->get('minPrice', 0),
            'maxPrice' => $request->query->get('maxPrice', 1000),
            'theme' => $request->query->get('theme'),
            'diet' => $request->query->get('diet'),
            'minPeople' => $request->query->get('minPeople'),
        ];
        $offset = (int) $request->query->get('offset', 6);

        $menus = $menuRepository->findFiltered($filters);
        $nextMenus = array_slice($menus, $offset, 6);

        $html = $this->renderView('components/menu_cards.html.twig', [
            'menus' => $nextMenus,
        ]);

        return $this->json([
            'html' => $html,
            'hasMore' => count($menus) > $offset + 6,
        ]);
    }
}
