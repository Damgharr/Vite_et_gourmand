<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]

    public function index(Request $request, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $subject = $request->request->get('subject');
            $content = $request->request->get('content');

            $errors = [];

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'adresse email n\'est pas valide.';
            }
            if (empty($subject)) {
                $errors[] = 'Le sujet est obligatoire.';
            }
            if (empty($content)) {
                $errors[] = 'Le message est obligatoire.';
            }

            if (empty($errors)) {
                $message = (new Email())
                    ->from($email)
                    ->to('dg@kobra.rocks')
                    ->subject($subject)
                    ->text($content);

                $mailer->send($message);
                $this->addFlash('success', 'Votre message a bien été envoyé.');
                return $this->redirectToRoute('contact');
            }

            return $this->render('pages/contact.html.twig', [
                'errors' => $errors,
                'email' => $email,
                'subject' => $subject,
                'content' => $content,
            ]);
        }

        return $this->render('pages/contact.html.twig');
    }
}

//0c59e12f343ee9c9b3e67cfbdb4ef058
