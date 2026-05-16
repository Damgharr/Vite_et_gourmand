<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\DietRepository;
use App\Repository\MenuRepository;
use App\Repository\OpeningHoursRepository;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/menus/{id}/delivery-price', name: 'menu_delivery_price', methods: ['POST'])]
    public function deliveryPrice(Request $request): JsonResponse
    {
        $address = $request->request->get('address');
        $city = $request->request->get('city');
        $zipCode = $request->request->get('zipCode');
        $country = $request->request->get('country', 'France');

        if (empty($address) || empty($city) || empty($zipCode)) {
            return $this->json(['error' => 'Adresse incomplète.'], 400);
        }

        $coords = $this->geocodeAddress($address, $city, $zipCode, $country);
        if (!$coords) {
            return $this->json(['error' => 'Adresse introuvable.'], 400);
        }

        $distance = $this->haversine(44.8378, -0.5792, $coords['lat'], $coords['lon']);
        $result = $this->calculateDeliveryPrice($distance);

        return $this->json($result);
    }

    #[Route('/menus/{id}/order', name: 'menu_order')]
    #[IsGranted('ROLE_USER')]
    public function order(\App\Entity\Menu $menu, Request $request, EntityManagerInterface $em, OpeningHoursRepository $openingHoursRepository): Response
    {
        $user = $this->getUser();
        $errors = [];

        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $phone = $request->request->get('phone');
            $email = $request->request->get('email');
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
            if ($peopleAmount < $menu->getMinPeopleAmount()) {
                $errors[] = 'Le nombre de personnes doit être au minimum de ' . $menu->getMinPeopleAmount() . '.';
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
            $deliveryPrice = '0.00';

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
                $coords = $this->geocodeAddress($address, $city, $zipCode, $country);
                if (!$coords) {
                    $errors[] = 'Adresse de livraison introuvable.';
                } else {
                    $distance = $this->haversine(44.8378, -0.5792, $coords['lat'], $coords['lon']);
                    $deliveryPrice = (string) $this->calculateDeliveryPrice($distance)['price'];
                }
            }

            if (empty($errors)) {
                $order = new Order();
                $order->setOrderNumber('ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5)));
                $order->setOrderDate(new \DateTimeImmutable());
                $order->setDatePrestation($datePrestation);
                $order->setDeliveryHour($deliveryHour);
                $order->setMenuPrice((string) ((float) $menu->getPricePerPeople() * $peopleAmount));
                $order->setDeliveryPrice($deliveryPrice);
                $order->setPeopleAmount($peopleAmount);
                $order->setStatus('en attente');
                $order->setMaterialLent(false);
                $order->setEquipmentReturn(false);
                $order->setAddress($address);
                $order->setCity($city);
                $order->setZipCode($zipCode);
                $order->setCountry($country);
                $order->setUser($user);
                $order->setMenu($menu);

                $em->persist($order);
                $em->flush();

                $this->addFlash('success', 'Votre commande a bien été passée.');
                return $this->redirectToRoute('menus');
            }

            return $this->render('pages/order.html.twig', [
                'menu' => $menu,
                'errors' => $errors,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'datePrestation' => $dateStr,
                'deliveryHour' => $timeStr,
                'peopleAmount' => $peopleAmount,
                'address' => $address,
                'city' => $city,
                'zipCode' => $zipCode,
                'country' => $country,
            ]);
        }

        return $this->render('pages/order.html.twig', [
            'menu' => $menu,
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail(),
            'peopleAmount' => $menu->getMinPeopleAmount(),
            'address' => $user->getAdress(),
            'city' => $user->getCity(),
            'zipCode' => $user->getZipCode(),
            'country' => $user->getCountry() ?? 'France',
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
