<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Order;
use App\Entity\OrderQuantity;
use App\Entity\Plat;
use App\Entity\User;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\OrderQuantityRepository;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
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
        ]);
    }
    
    /**
     * @Route("/profil/order/progress", name="profil.order.progress", methods={"GET"})
     */
    public function ProgressList(OrderRepository $orderRepo)
    {
        $orders = $this->PREFABorderList($orderRepo);
        $orderEncours = $orders[0];
        return $this->render('user/orderList.html.twig', [
            'orders' => $orderEncours,
        ]);

    }

    /**
     * @Route("/profil/order/delivered", name="profil.order.delivered", methods={"GET"})
     */
    public function DeliveredList(OrderRepository $orderRepo)
    {
        $orders = $this->PREFABorderList($orderRepo);
        $orderLivre = $orders[1];
        return $this->render('user/orderList.html.twig', [
            'orders' => $orderLivre,
        ]);
    }

    /**
     * @Route("/profil/order/delivered/{order}", name="profil.order.show", methods={"GET"})
     */
    public function showOrder(Order $order, OrderQuantityRepository $repo)
    {
        $orderQuantities = $repo->findBy([
            'orders' => $order
        ]);
        return $this->render('user/orderMore.html.twig', [
            'orders' => $orderQuantities,
        ]);
    }

    /**
     * @Route("/profil/order/delivered/{order}/{plat}", name="profil.order.note", methods={"GET","POST"})
     */
    public function note(Order $order, Plat $plat, Request $request, EntityManagerInterface $em, NoteRepository $noteRepo)
    {
        $user = $this->getUser();
        $note = new Note();
        $form = $this->createForm(NoteType::class,$note);
        $form->handleRequest($request);

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
            
            return $this->redirectToRoute('profil.order');
        }

        return $this->render('user/newNote.html.twig',[
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }
}
