<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Order;
use App\Entity\OrderQuantity;
use App\Entity\Plat;
use App\Entity\User;
use App\Form\NoteType;
use App\Form\UserFormType;
use App\Repository\NoteRepository;
use App\Repository\OrderQuantityRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil/", name="profil.show", methods={"GET"}) 
     */
    public function show(UserRepository $userRepo): Response
    {
        $user=$this->getUser();
        $user=$userRepo->find($user->getId());
        return $this->render('user/profil.html.twig', [
            'user' => $user,
        ]);
    }
    /**
     * @Route("/profil/edit", name="profil.edit", methods={"GET","POST"})
     */
    public function editUser(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $user=$this->getUser();
        $user=$userRepo->find($user->getId());
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('profil.show');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("admin/profil/{id}/edit", name="admin.profil.edit", methods={"GET","POST"})
     */
    public function adminEditUser(Request $request, EntityManagerInterface $em, User $user): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('profil.show');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    public function PREFABorderList(OrderRepository $orderRepo)
    {
        $orders = $orderRepo->findBy(['user'=>$this->getUser()]);
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
     * @Route("/profil/order", name="profil.order", methods={"GET"})
     */
    public function orderList(OrderRepository $orderRepo)
    {
        $orders = $this->PREFABorderList($orderRepo);
        $orders = array_merge($orders[0], $orders[1]);
        return $this->render('user/orderList.html.twig', [
            'orders' => $orders,
            'title' => 'Toutes vos commandes',
            'empty' => 'Aucune commande',
        ]);
    }
    
    /**
     * @Route("/profil/orders/progress", name="profil.order.progress", methods={"GET"})
     */
    public function ProgressList(OrderRepository $orderRepo)
    {
        $orders = $this->PREFABorderList($orderRepo);
        $orderEncours = $orders[0];
        return $this->render('user/orderList.html.twig', [
            'orders' => $orderEncours,
            'title' => 'Vos commandes en cours',
            'empty' => 'Aucune commande en cours'
        ]);

    }

    /**
     * @Route("/profil/order/delivered", name="profil.order.delivered", methods={"GET"})
     */
    public function DeliveredList(OrderRepository $orderRepo)
    {
        $orders = $this->PREFABorderList($orderRepo);
        $orderLivre = $orders[1];
        return $this->render('user/orderDelivered.html.twig', [
            'orders' => $orderLivre,
            'title' => 'Vos commandes livrées',
            'empty' => 'Aucune commande livrée'
        ]);
    }

    /**
     * @Route("/profil/order/delivered/{id}", name="profil.order.show", methods={"GET"})
     */
    public function showOrder(Order $order, OrderQuantityRepository $repo)
    {
        $user = $this->getUser();
        $verifUser = $order->getUser();
        if($verifUser == $user){
            $orderQuantities = $repo->findBy([
                'orders' => $order
            ]);
            return $this->render('user/orderMore.html.twig', [
                'orders' => $orderQuantities,
            ]);
        }else{
            return $this->redirectToRoute('profil.order.delivered');
        }
    }

    /**
     * @Route("/profil/order/delivered/{order}/{plat}", name="profil.order.note", methods={"GET","POST"})
     */
    public function note(Order $order, Plat $plat, Request $request, EntityManagerInterface $em, NoteRepository $noteRepo, OrderQuantityRepository $orderRepo)
    {
        $user = $this->getUser();
        $note = new Note();
        $form = $this->createForm(NoteType::class,$note);
        $form->handleRequest($request);
        $verifUser = $order->getUser();
        $verifOrders = $orderRepo->findBy([
            'plats' => $plat
        ]);
        foreach($verifOrders as $verifOrder){
            if($verifOrder->getOrders() == $order){
                $verif = true;
                $verifOrderDate = $verifOrder->getOrders();
            }
        }
        $datehier = New DateTime('-1 day +1 hour');
        $dateLivre = New DateTime();
        $verifDate = $verifOrderDate->getOrderedAt();
        if($verifUser == $user && $verif == true ){
            if($dateLivre > $verifDate && $verifDate > $datehier){
                if ($form->isSubmitted() && $form->isValid()) {
                    $existingNote = $noteRepo->findOneBy([
                        'plats' => $plat,
                        'user' => $user
                    ]);
        
                    $noteplats = $noteRepo->findBy([
                        'plats' => $plat
                    ]);
                    $notes = [];
                    foreach($noteplats as $noteplat){
                        $notes[] = $noteplat->getNote();
                    }
            
                    if($existingNote == null){
                        $note->setPlats($plat)
                            ->setUser($user);
        
                        $notes[] = $note->getNote();
                        $em->persist($note);
                    }else{
                        if(count($notes) == 1){
                            $notes = [];
                        }else{
                            $lanote = $existingNote->getNote();
                            unset($notes[array_search($lanote,$notes)]);
                        }
                        $existingNote->setNote($note->getNote());
                        $notes[] = $note->getNote();
                    }
    
                    $noteMoyenne = array_sum($notes)/(count($notes));
                    $plat->setNoteMoyenne($noteMoyenne);
                    $em->flush();
                    
                    return $this->redirectToRoute('profil.order.delivered');
                }
                return $this->render('user/newNote.html.twig',[
                    'plat' => $plat,
                    'form' => $form->createView(),
                ]);
            }else{
                return $this->redirectToRoute('profil.order.delivered');
            }
            
            
        }else{
            return $this->redirectToRoute('profil.order.delivered');
        }
    }
}
