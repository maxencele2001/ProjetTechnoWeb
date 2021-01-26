<?php

namespace App\Controller;


use DateTime;
use App\Repository\TypeRepository;
use App\Repository\RestaurantRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\NoteRepository;
use App\Entity\User;
use App\Form\UserFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PlatRepository;
class DashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin.dashboard", methods={"GET"})
     */
    public function adminDashboard(RestaurantRepository $repo, OrderRepository $orderRepo, UserRepository $userRepo, TypeRepository $typeRepo, PlatRepository $platRepository)
    {
        $types = $typeRepo->findAll();
        $restaurants = $repo->findAll();
        $users = $userRepo->findAll();
        $plats = $platRepository->findAll();
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
            'plats' => $plats,
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
     * @Route("/admin/order", name="dashboard.order", methods={"GET"})
     */
    public function orderList(RestaurantRepository $repo, OrderRepository $orderRepo)
    {
        $orders = $this->PREFABorderList($repo,$orderRepo);
        //$orders = $restaurant->getOrders(); //autre façon de le faire
        $orders = array_merge($orders[0], $orders[1]);
        return $this->render('dashboard/orderList.html.twig', [
            'orders' => $orders,
            'title' => 'Toutes les commandes',
            'empty' => 'Aucune commande'
        ]);
    }

    /**
     * @Route("/admin/order/progress", name="dashboard.order.progress", methods={"GET"})
     */
    public function ProgressList(RestaurantRepository $repo, OrderRepository $orderRepo )
    {
        $orders = $this->PREFABorderList($repo,$orderRepo);
        $orderEncours = $orders[0];
        return $this->render('dashboard/orderList.html.twig', [
            'orders' => $orderEncours,
            'title' => 'Toutes les commandes en cours',
            'empty' => 'Aucune commande en cours'
        ]);

    }
    /**
     * @Route("/admin/order/delivered", name="dashboard.order.delivered", methods={"GET"})
     */
    public function DeliveredList(RestaurantRepository $repo, OrderRepository $orderRepo )
    {
        $orders = $this->PREFABorderList($repo,$orderRepo);
        $orderLivre = $orders[1];
        return $this->render('dashboard/orderList.html.twig', [
            'orders' => $orderLivre,
            'title' => 'Toutes les commandes livrées',
            'empty' => 'Aucune commande en livraison'
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
        $user=$this->getUser();
        $user=$userRepo->find($user->getId());
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
            'user'=> $user,
            'orderEncours' => $orderEncours,
            'orderLivre' => $orderLivre,
            'restaurateur' => $restaurateur
        ]); 
    }


    /**
     * @Route("/admin/users", name="users_index", methods={"GET"})
     */
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('dashboard/usersList.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    ##################### Commandes USER #############################

    public function PREFABorderListUser(OrderRepository $orderRepo, User $user)
    {
        $orders = $orderRepo->findBy(['user'=> $user]);
        $orderList = [];
        $orderEncours = [];
        $orderLivre = [];
        foreach($orders as $order)
        {
            if($order->getOrderedAt()< new DateTime()){
                $orderLivre[] = $orderRepo->find($order);
            }else{
                $orderEncours[] = $orderRepo->find($order);
            }
        }

        $orderList = [$orderEncours,$orderLivre];
        return $orderList;
    }

    /**
     * @Route("/admin/user/{id}", name="user_history", methods={"GET"})
     */
    public function userHistory(OrderRepository $orderRepo, User $user)
    {
        $orders = $this->PREFABorderListUser($orderRepo, $user);
        $orderEncours = $orders[0];
        $orderLivre = $orders[1];
        return $this->render('dashboard/userHistory.html.twig', [
            'orders' => $orders,
            'orderEncours' => $orderEncours,
            'orderLivre' => $orderLivre,
        ]);
    }

     /**
     * @Route("/admin/profil/{id}/edit", name="admin.profil.edit", methods={"GET","POST"})
     */
    public function adminEditUser(Request $request, EntityManagerInterface $em, User $user): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('users_index');
        }
        return $this->render('dashboard/userEdit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    
    /**
     * @Route("/admin/notes", name="resto_notes", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function Avis(NoteRepository $noteRepo)
    {
        $notes = $noteRepo->findAll();  
        return $this->render('dashboard/notesHistory.html.twig', [
            'notes' => $notes,
        ]);

    }
    
}
