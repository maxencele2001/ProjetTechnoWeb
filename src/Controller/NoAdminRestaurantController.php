<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoAdminRestaurantController extends AbstractController
{
    /**
     * @Route("/restaurant", name="home")
     */
    public function index(RestaurantRepository $repo): Response
    {
        $restaurants = $repo->findAll();
        return $this->render('no_admin_restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * @Route("/restaurant/{id}", name="restaurant.show")
     */
    public function showOne(Restaurant $restaurant): Response
    {
        return $this->render('no_admin_restaurant/restaurant.html.twig', [
            'restaurant'=>$restaurant,
        ]);
    }
}
