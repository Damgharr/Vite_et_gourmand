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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, UserRepository $userRepository, TokenStorageInterface $tokenStorage): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('passwordConfirm');
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $phone = $request->request->get('phone');
            $country = $request->request->get('country');
            $city = $request->request->get('city');
            $zipCode = $request->request->get('zipCode');
            $adress = $request->request->get('adress');

            $errors = [];

            if (empty($firstName)) {
                $errors[] = 'Le prénom est obligatoire.';
            }
            if (empty($lastName)) {
                $errors[] = 'Le nom est obligatoire.';
            }
            if (empty($phone)) {
                $errors[] = 'Le numéro de téléphone est obligatoire.';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'adresse email n\'est pas valide.';
            }
            if ($userRepository->findOneBy(['email' => $email])) {
                $errors[] = 'Cet email est déjà utilisé.';
            }
            if (empty($password)) {
                $errors[] = 'Le mot de passe est obligatoire.';
            } else {
                if (strlen($password) < 10) {
                    $errors[] = 'Le mot de passe doit contenir au moins 10 caractères.';
                }
                if (!preg_match('/[A-Z]/', $password)) {
                    $errors[] = 'Le mot de passe doit contenir au moins une majuscule.';
                }
                if (!preg_match('/[a-z]/', $password)) {
                    $errors[] = 'Le mot de passe doit contenir au moins une minuscule.';
                }
                if (!preg_match('/[0-9]/', $password)) {
                    $errors[] = 'Le mot de passe doit contenir au moins un chiffre.';
                }
                if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                    $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial.';
                }
            }
            if ($password !== $passwordConfirm) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }

            if (empty($errors)) {
                $user = new User();
                $user->setEmail($email);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setPhone($phone);
                $user->setCountry($country ?: null);
                $user->setCity($city ?: null);
                $user->setZipCode($zipCode ?: null);
                $user->setAdress($adress ?: null);
                $user->setRoles(['ROLE_USER']);
                $user->setEnabled(true);
                $user->setPassword($hasher->hashPassword($user, $password));

                $em->persist($user);
                $em->flush();

                $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
                $tokenStorage->setToken($token);
                $request->getSession()->set('_security_main', serialize($token));

                return $this->redirectToRoute('homepage');
            }

            return $this->render('pages/register.html.twig', [
                'errors' => $errors,
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $phone,
                'country' => $country,
                'city' => $city,
                'zipCode' => $zipCode,
                'adress' => $adress,
            ]);
        }

        return $this->render('pages/register.html.twig');
    }
}
