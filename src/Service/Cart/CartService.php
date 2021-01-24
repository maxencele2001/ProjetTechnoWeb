<?php

namespace App\Service\Cart;

use Symfony\Component\Mime\Email;
use App\Entity\Order;
use App\Entity\OrderQuantity;
use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Repository\PlatRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use App\Repository\MailC;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\SwiftMailerHandler;
use Swift;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class CartService{
    
    protected $session;
    protected $repoPlat;
    protected $repoRes;
    protected $repoUser;
    protected $em;
    public $idResto;
    private $renderer;
    private $mailer;
    

    public function __construct(SessionInterface $session, PlatRepository $repoPlat, EntityManagerInterface $em, RestaurantRepository $repoRes, UserRepository $repoUser, Environment $renderer, \Swift_Mailer $mailer)
    {
        $this->session = $session;
        $this->repoPlat = $repoPlat;
        $this->em = $em;
        $this->repoRes = $repoRes;
        $this->repoUser = $repoUser;
        $this->renderer = $renderer;
        $this->mailer = $mailer;
    }

    public function add(Plat $plat)
    {
        $panier = $this->session->get('panier', []);
        $Resto = $this->session->get('resto', []);
        $idResto = $plat->getRestaurants()->getId();

        if(empty($panier)){
            $this->session->set('resto',$idResto);
            $Resto = $this->session->get('resto', []);
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
        if($user->getBalance()>= $this->getTotal()+2.5)
        {
            $order = new Order();
            $order->setOrderedAt(new DateTime('+1 hour'));//faire gaffe a l'heure
            $order->setPriceTotal($this->getTotal()+2.5);
            $order->setRestaurant($resto);
            $order->setUser($user);
            
            $user->setBalance($user->getBalance()-($this->getTotal()+2.5));
            $resto->setBalance($resto->getBalance()+$this->getTotal());
            $admin = $this->repoUser->findOneBy([
                'name' => "admin"
            ]);//à modifier pour find by roles pour plus de securité
            $admin->setBalance($admin->getBalance()+2.5);
            $this->em->persist($order);
                
            foreach($this->getFullCart() as $item){
                $orderQuantity = new OrderQuantity();
                $orderQuantity->setOrders($order);
                $orderQuantity->setPlats($item['plat']);
                $orderQuantity->setQuantity($item['quantity']);
                $this->em->persist($orderQuantity);
            }
            $this->em->flush();
            $message = (new \Swift_Message('Commande N° ' . $order->getId()))
                ->setFrom('send@example.com')
                ->setTo($resto->getEmail())
                ->setBody(
                    $this->renderer->render(
                        // templates/emails/registration.html.twig
                        'emails/registration.html.twig',
                        ['id' => $order->getId(),
                        'adress' => $user->getAddress(),
                        'total' => $this->getTotal()+2.5,
                        'items' => $this->getFullCart(),
                        'time' => $order->getOrderedAt(),
                        ]
                    ),
                    'text/html'
                );
            $this->mailer->send($message);
        }


        
    }
    /*
    public function email(){
        $resto = $this->repoRes->find($this->session->get('resto'));//penser a find avec un objet
        $email=$resto->getEmail();
        $info=('test');
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo($email)
            ->setBody(
                $this->renderer->render(
                    // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['info' => $info]
                ),
                'text/html'
            );
            $this->mailer->send($message);
    
    }
    */
}