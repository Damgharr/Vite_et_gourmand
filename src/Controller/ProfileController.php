<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Review;
use App\Repository\OpeningHoursRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'profile')]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->createQueryBuilder('o')
            ->leftJoin('o.menu', 'm')
            ->addSelect('m')
            ->leftJoin('o.reviews', 'r')
            ->addSelect('r')
            ->where('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.orderDate', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('pages/profile.html.twig', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    #[Route('/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $errors = [];

        if ($request->isMethod('POST')) {
            $firstName = trim($request->request->get('firstName'));
            $lastName = trim($request->request->get('lastName'));
            $phone = trim($request->request->get('phone'));
            $email = trim($request->request->get('email'));
            $address = trim($request->request->get('address'));
            $city = trim($request->request->get('city'));
            $zipCode = trim($request->request->get('zipCode'));
            $country = trim($request->request->get('country'));

            if (empty($firstName)) {
                $errors[] = 'Le prénom est obligatoire.';
            }
            if (empty($lastName)) {
                $errors[] = 'Le nom est obligatoire.';
            }
            if (empty($phone)) {
                $errors[] = 'Le téléphone est obligatoire.';
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email est invalide.';
            }

            if (empty($errors)) {
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setPhone($phone);
                $user->setEmail($email);
                $user->setAdress($address ?: null);
                $user->setCity($city ?: null);
                $user->setZipCode($zipCode ?: null);
                $user->setCountry($country ?: null);
                $em->flush();

                $this->addFlash('success', 'Vos informations ont été mises à jour.');
                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('pages/profile_edit.html.twig', [
            'user' => $user,
            'errors' => $errors,
        ]);
    }

    #[Route('/orders/{id}/edit', name: 'profile_order_edit', methods: ['GET', 'POST'])]
    public function editOrder(Order $order, Request $request, EntityManagerInterface $em, OpeningHoursRepository $openingHoursRepository): Response
    {
        $user = $this->getUser();
        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        if ($order->getStatus() !== 'en attente') {
            $this->addFlash('error', 'Cette commande ne peut plus être modifiée.');
            return $this->redirectToRoute('profile');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $dateStr = $request->request->get('datePrestation');
            $timeStr = $request->request->get('deliveryHour');
            $peopleAmount = (int) $request->request->get('peopleAmount');
            $address = $request->request->get('address');
            $city = $request->request->get('city');
            $zipCode = $request->request->get('zipCode');
            $country = $request->request->get('country', 'France');

            if (empty($dateStr)) {
                $errors[] = 'La date de livraison est obligatoire.';
            }
            if (empty($timeStr)) {
                $errors[] = 'L\'heure de livraison est obligatoire.';
            }
            if ($peopleAmount < $order->getMenu()->getMinPeopleAmount()) {
                $errors[] = 'Le nombre de personnes doit être au minimum de ' . $order->getMenu()->getMinPeopleAmount() . '.';
            }
            if (empty($address)) {
                $errors[] = 'L\'adresse est obligatoire.';
            }
            if (empty($city)) {
                $errors[] = 'La ville est obligatoire.';
            }
            if (empty($zipCode)) {
                $errors[] = 'Le code postal est obligatoire.';
            }

            $datePrestation = null;
            $deliveryHour = null;

            if (empty($errors) && $dateStr && $timeStr) {
                $datePrestation = new \DateTime($dateStr);
                $deliveryHour = new \DateTime($timeStr);

                $dayNames = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                $dayName = $dayNames[(int) $datePrestation->format('w')];
                $hours = $openingHoursRepository->findOneBy(['day' => $dayName]);

                if (!$hours || !$hours->getOpenTime() || !$hours->getCloseTime()) {
                    $errors[] = 'Nous sommes fermés ce jour-là.';
                } else {
                    $timeValue = $deliveryHour->format('H:i');
                    $openTime = $hours->getOpenTime()->format('H:i');
                    $closeTime = $hours->getCloseTime()->format('H:i');
                    if ($timeValue < $openTime || $timeValue > $closeTime) {
                        $errors[] = 'Nous sommes fermés à cette heure-ci.';
                    }
                }
            }

            if (empty($errors)) {
                $order->setDatePrestation($datePrestation);
                $order->setDeliveryHour($deliveryHour);
                $order->setPeopleAmount($peopleAmount);
                $order->setMenuPrice((string) ((float) $order->getMenu()->getPricePerPeople() * $peopleAmount));
                $order->setAddress($address);
                $order->setCity($city);
                $order->setZipCode($zipCode);
                $order->setCountry($country);

                $coords = $this->geocodeAddress($address, $city, $zipCode, $country);
                if ($coords) {
                    $distance = $this->haversine(44.8378, -0.5792, $coords['lat'], $coords['lon']);
                    $deliveryPrice = $this->calculateDeliveryPrice($distance)['price'];
                    $order->setDeliveryPrice((string) $deliveryPrice);
                }

                $em->flush();
                $this->addFlash('success', 'Votre commande a bien été modifiée.');
                return $this->redirectToRoute('profile');
            }

            return $this->render('pages/order_edit.html.twig', [
                'order' => $order,
                'errors' => $errors,
                'datePrestation' => $dateStr,
                'deliveryHour' => $timeStr,
                'peopleAmount' => $peopleAmount,
                'address' => $address,
                'city' => $city,
                'zipCode' => $zipCode,
                'country' => $country,
            ]);
        }

        return $this->render('pages/order_edit.html.twig', [
            'order' => $order,
            'datePrestation' => $order->getDatePrestation()->format('Y-m-d'),
            'deliveryHour' => $order->getDeliveryHour()->format('H:i'),
            'peopleAmount' => $order->getPeopleAmount(),
            'address' => $order->getAddress(),
            'city' => $order->getCity(),
            'zipCode' => $order->getZipCode(),
            'country' => $order->getCountry() ?? 'France',
        ]);
    }

    #[Route('/orders/{id}/cancel', name: 'profile_order_cancel', methods: ['POST'])]
    public function cancelOrder(Order $order, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        if ($order->getStatus() !== 'en attente') {
            $this->addFlash('error', 'Cette commande ne peut plus être annulée.');
            return $this->redirectToRoute('profile');
        }

        $order->setStatus('annulée');
        $em->flush();
        $this->addFlash('success', 'Votre commande a été annulée.');
        return $this->redirectToRoute('profile');
    }

    #[Route('/orders/{id}/review', name: 'profile_order_review', methods: ['GET', 'POST'])]
    public function reviewOrder(Order $order, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($order->getUser() !== $user) {
            throw $this->createAccessDeniedException();
        }
        if ($order->getStatus() !== 'terminée') {
            $this->addFlash('error', 'Vous ne pouvez pas donner d\'avis sur cette commande.');
            return $this->redirectToRoute('profile');
        }
        if (!$order->getReviews()->isEmpty()) {
            $this->addFlash('error', 'Vous avez déjà donné votre avis sur cette commande.');
            return $this->redirectToRoute('profile');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $note = (int) $request->request->get('note');
            $text = trim($request->request->get('text'));

            if ($note < 1 || $note > 5) {
                $errors[] = 'La note doit être entre 1 et 5 étoiles.';
            }

            if (empty($errors)) {
                $review = new Review();
                $review->setNote($note);
                $review->setText($text ?: null);
                $review->setStatus('en attente');
                $review->setUser($user);
                $review->setOrder($order);

                $em->persist($review);
                $em->flush();

                $this->addFlash('success', 'Merci pour votre avis !');
                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('pages/order_review.html.twig', [
            'order' => $order,
            'errors' => $errors,
        ]);
    }

    private function geocodeAddress(string $address, string $city, string $zipCode, string $country): ?array
    {
        $query = urlencode($address . ', ' . $zipCode . ' ' . $city . ', ' . $country);
        $url = 'https://nominatim.openstreetmap.org/search?q=' . $query . '&format=json&limit=1';
        $opts = ['http' => ['header' => 'User-Agent: ViteEtGourmand/1.0', 'timeout' => 5]];
        $response = @file_get_contents($url, false, stream_context_create($opts));
        if (!$response) {
            return null;
        }
        $data = json_decode($response, true);
        if (empty($data)) {
            return null;
        }
        return [
            'lat' => (float) $data[0]['lat'],
            'lon' => (float) $data[0]['lon'],
        ];
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function calculateDeliveryPrice(float $distance): array
    {
        $freeThreshold = 15.0;
        if ($distance <= $freeThreshold) {
            return ['price' => 0.0, 'distance' => round($distance, 2), 'free' => true];
        }
        $price = 5.0 + ($distance * 0.59);
        return ['price' => round($price, 2), 'distance' => round($distance, 2), 'free' => false];
    }
}
