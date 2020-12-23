<?php

namespace App\Service\Cart;

use App\Repository\PlatRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService{
    
    protected $session;
    protected $repoPlat;

    public function __construct(SessionInterface $session, PlatRepository $repoPlat)
    {
        $this->session = $session;
        $this->repoPlat = $repoPlat;
    }

    public function add(int $id)
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
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
}