<?php

namespace App\Controller;


use DateTime;
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
    public function adminDashboard(RestaurantRepository $repo, OrderRepository $orderRepo, UserRepository $userRepo)
    {
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
            'orderEncours' => $orderEncours,
            'orderLivre' => $orderLivre,
            'restaurateur' => $restaurateur //admin
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
