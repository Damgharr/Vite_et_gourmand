<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminThemeController extends AbstractController
{
    #[Route('/themes', name: 'admin_themes')]
    public function index(ThemeRepository $themeRepository): Response
    {
        return $this->render('admin/themes/index.html.twig', [
            'themes' => $themeRepository->findAll(),
        ]);
    }

    #[Route('/themes/new', name: 'admin_themes_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $theme = new Theme();
            $theme->setLabel($request->request->get('label'));
            $em->persist($theme);
            $em->flush();
            $this->addFlash('success', 'Thème créé.');
            return $this->redirectToRoute('admin_themes');
        }
        return $this->render('admin/themes/new.html.twig');
    }

    #[Route('/themes/{id}/edit', name: 'admin_themes_edit', methods: ['GET', 'POST'])]
    public function edit(Theme $theme, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $theme->setLabel($request->request->get('label'));
            $em->flush();
            $this->addFlash('success', 'Thème modifié.');
            return $this->redirectToRoute('admin_themes');
        }
        return $this->render('admin/themes/edit.html.twig', ['theme' => $theme]);
    }

    #[Route('/themes/{id}/delete', name: 'admin_themes_delete', methods: ['POST'])]
    public function delete(Theme $theme, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $theme->getId(), $request->request->get('_token'))) {
            $em->remove($theme);
            $em->flush();
            $this->addFlash('success', 'Thème supprimé.');
        }
        return $this->redirectToRoute('admin_themes');
    }
}
