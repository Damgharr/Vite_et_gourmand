<?php

namespace App\Controller;

use App\Entity\Dish;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DishPhotoController extends AbstractController
{
    #[Route('/dish/{id}/photo', name: 'dish_photo')]
    public function photo(Dish $dish): Response
    {
        $photo = $dish->getPhoto();
        if (empty($photo)) {
            return new Response('', 404);
        }
        $data = is_resource($photo) ? stream_get_contents($photo) : $photo;
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($data);
        return new Response($data, 200, ['Content-Type' => $mime ?: 'image/jpeg']);
    }
}
