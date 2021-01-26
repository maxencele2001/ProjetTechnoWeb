<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Form\SearchRestaurantType;
use App\Repository\RestaurantRepository;
use App\Security\Voter\RestaurateurVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Regex;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="search", methods={"GET", "POST"})
     */
    public function Home(RestaurantRepository $repo, Request $request): Response
    {
        $restaurants = $repo->findTop(Restaurant::NB_HOME);
        $form = $this->createForm(SearchRestaurantType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $restaurantName = $form->getData()->getName();
            $restaurants = $repo->search($restaurantName);
        }

        return $this->render('homepage/index.html.twig', [
            'restaurants' => $restaurants,
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * @Route("/restaurants/restaurant?", name="TestSearch")
     */
    public function search(RestaurantRepository $repo, Request $request): Response
    {
        $param = $request->query->get('restaurant');
        $data['restaurant'] = $repo->findBy([
            'name' => $param,
        ]);
        $restaurants = $data['restaurant'];
        return $this->render('no_admin_restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
}
