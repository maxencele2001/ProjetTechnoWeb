<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(RestaurantRepository $repo): Response
    {
        $restaurants = $repo->findAll();
        return $this->render('no_admin_restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
}
