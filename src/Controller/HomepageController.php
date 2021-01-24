<?php

namespace App\Controller;

use App\Entity\Restaurant;
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
        $restaurants = $repo->findTop(Restaurant::NB_HOME);
        return $this->render('homepage/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
    
    
}
