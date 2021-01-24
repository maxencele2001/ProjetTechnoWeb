<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    protected $repoUser;
    /**
     * @Route("/cart", name="cart.index")
     */
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig',[
            'items'=> $cartService->getFullCart(),
            'total'=> $cartService->getTotal(),
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart.add")
     */
    public function add(Plat $plat, CartService $cartService)
    {
        $cartService->add($plat);
        return $this->redirectToRoute('cart.index');
    }

    /**
     * @Route("/cart/remove/{id}", name="cart.remove")
     */
    public function remove($id,CartService $cartService)
    {
        $cartService->remove($id);
        return $this->redirectToRoute('cart.index');
    }

    /**
     * @Route("/cart/order", name="cart.order")
     */
    public function order(CartService $cartService)
    {
        $cartService->order($this->getUser());  
        return $this->redirectToRoute('homepage');
    }
}
  