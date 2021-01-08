<?php

namespace App\Service\Cart;

use App\Entity\Order;
use App\Entity\OrderQuantity;
use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\PlatRepository;
use App\Repository\RestaurantRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService{
    
    protected $session;
    protected $repoPlat;
    protected $repoRes;
    protected $em;
    public $idResto;
    

    public function __construct(SessionInterface $session, PlatRepository $repoPlat, EntityManagerInterface $em, RestaurantRepository $repoRes)
    {
        $this->session = $session;
        $this->repoPlat = $repoPlat;
        $this->em = $em;
        $this->repoRes = $repoRes;
    }

    public function add(Plat $plat)
    {
        $panier = $this->session->get('panier', []);
        $Resto = $this->session->get('resto', []);
        $idResto = $plat->getRestaurants()->getId();

        if(empty($panier)){
            $this->session->set('resto',$idResto);
        }
        if(!empty($panier[$plat->getId()])){
            if($Resto == $idResto){
                $panier[$plat->getId()]++;
            } 
        }else{
            if($Resto == $idResto){
                $panier[$plat->getId()] = 1;
            } 
        }
        $this->session->set('panier',$panier);
    }

    public function remove(int $id)
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $this->session->set('panier',$panier);
    }

    public function getFullCart():array 
    {
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id=>$quantity){
            $panierWithData[] =[ 
                'plat' => $this->repoPlat->find($id),
                'quantity'=> $quantity
            ];
        }

        return $panierWithData;
    }

    public function getTotal():float 
    {
        $total = 0;
        foreach($this->getFullCart() as $item){
            $total += $item['plat']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function order(User $user)
    {
        $resto = $this->repoRes->find($this->session->get('resto'));//penser a find avec un objet
        $order = new Order();
        $order->setOrderedAt(new DateTime('+1 hour'));//faire gaffe a l'heure
        $order->setPriceTotal($this->getTotal());
        $order->setRestaurant($resto);
        $order->setUser($user);
        $user->setBalance($user->getBalance()-($this->getTotal()+2.5));
        $resto->setBalance($resto->getBalance()+$this->getTotal());
        $this->em->persist($order);
            
        foreach($this->getFullCart() as $item){
            $orderQuantity = new OrderQuantity();
            $orderQuantity->setOrders($order);
            $orderQuantity->setPlats($item['plat']);
            $orderQuantity->setQuantity($item['quantity']);
            $this->em->persist($orderQuantity);
        }
        $this->em->flush();
    }
}