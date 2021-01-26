<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/restaurants/searchByName", name="search")
     */
    public function search(RestaurantRepository $repo, Request $request): Response
    {
        $param = $request->query->get('r');
        $data['restaurant'] = $repo->findBy([
            'name' => $param,
        ]);

        $restaurants = $data['restaurant'];
        return $this->render('no_admin_restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
    
    
}
