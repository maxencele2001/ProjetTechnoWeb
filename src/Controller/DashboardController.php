<?php

namespace App\Controller;


use DateTime;
use App\Repository\TypeRepository;
use App\Repository\RestaurantRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin.dashboard", methods={"GET"})
     */
    public function adminDashboard(RestaurantRepository $repo, OrderRepository $orderRepo, UserRepository $userRepo, TypeRepository $typeRepo)
    {
        $types = $typeRepo->findAll();
        $restaurants = $repo->findAll();
        $users = $userRepo->findAll();
        $restaurateur = $userRepo->find($this->getUser()); // session admin
        $orderEncours = [];
        $orderLivre = [];
        foreach ($restaurants as $restaurant){
            $resto = $repo->find($restaurant);
            $orderResto = $orderRepo->findBy(
                ['restaurant' => $resto]
            );
            foreach ($orderResto as $order){
                if($order->getOrderedAt()< new DateTime()){
                    $orderLivre[] = $orderRepo->find($order);
                }else{
                    $orderEncours[] = $orderRepo->find($order);
                }
            }        
        }

        return $this->render('dashboard/adminDashboard.html.twig', [
            'restaurants' => $restaurants,
            'users' => $users,
            'types' => $types,
            'orderEncours' => $orderEncours,
            'orderLivre' => $orderLivre,
            'restaurateur' => $restaurateur //admin
        ]); 
    }

    public function PREFABorderList(RestaurantRepository $repo, OrderRepository $orderRepo)
    {
        $restaurants = $repo->findALl();
        $orderEncours = [];
        $orderLivre = [];
        foreach ($restaurants as $restaurant){
            $resto = $repo->find($restaurant);
            $orderResto = $orderRepo->findBy(
                ['restaurant' => $resto]
            );
            foreach ($orderResto as $order){
                if($order->getOrderedAt()< new DateTime()){
                    $orderLivre[] = $orderRepo->find($order);
                }else{
                    $orderEncours[] = $orderRepo->find($order);
                }
            }        
        }     
        
        $orders = [$orderEncours,$orderLivre];
    
        return $orders;
    }

    /**
     * @Route("/order", name="dashboard.order", methods={"GET"})
     */
    public function orderList(RestaurantRepository $repo, OrderRepository $orderRepo)
    {
        $orders = $this->PREFABorderList($repo,$orderRepo);
        //$orders = $restaurant->getOrders(); //autre façon de le faire
        $orders = array_merge($orders[0], $orders[1]);
        return $this->render('dashboard/orderList.html.twig', [
            'orders' => $orders,
            'title' => 'Toutes les commandes'
        ]);
    }

    /**
     * @Route("/order/progress", name="dashboard.order.progress", methods={"GET"})
     */
    public function ProgressList(RestaurantRepository $repo, OrderRepository $orderRepo )
    {
        $orders = $this->PREFABorderList($repo,$orderRepo);
        $orderEncours = $orders[0];
        return $this->render('dashboard/orderList.html.twig', [
            'orders' => $orderEncours,
            'title' => 'Toutes les commandes en cours'
        ]);

    }
    /**
     * @Route("/order/delivered", name="dashboard.order.delivered", methods={"GET"})
     */
    public function DeliveredList(RestaurantRepository $repo, OrderRepository $orderRepo )
    {
        $orders = $this->PREFABorderList($repo,$orderRepo);
        $orderLivre = $orders[1];
        return $this->render('dashboard/orderList.html.twig', [
            'orders' => $orderLivre,
            'title' => 'Toutes les commandes livrées'
        ]);
    }

    /**
     * @Route("/restaurateur", name="restaurateur.dashboard", methods={"GET"})
     */
    public function restateurDashboard(RestaurantRepository $repo, OrderRepository $orderRepo, UserRepository $userRepo)
    {
        $restaurants = $repo->getByIdUser($this->getUser());
        $restaurateur = $userRepo->find($this->getUser());
        $orderEncours = [];
        $orderLivre = [];
        foreach ($restaurants as $restaurant){
            $resto = $repo->find($restaurant);
            $orderResto = $orderRepo->findBy(
                ['restaurant' => $resto]
            );
            foreach ($orderResto as $order){
                if($order->getOrderedAt()< new DateTime()){
                    $orderLivre[] = $orderRepo->find($order);
                }else{
                    $orderEncours[] = $orderRepo->find($order);
                }
            }        
        }

        return $this->render('dashboard/restaurateurDashboard.html.twig', [
            'restaurants' => $restaurants,
            'orderEncours' => $orderEncours,
            'orderLivre' => $orderLivre,
            'restaurateur' => $restaurateur
        ]); 
    }


    
}
