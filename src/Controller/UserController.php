<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Plat;
use App\Entity\User;
use App\Form\NoteType;
use App\Form\UserFormType;
use App\Repository\NoteRepository;
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
            return $this->redirectToRoute('profil.order');
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
     * @Route("/profil/order/delivered/{id}", name="profil.order.note", methods={"GET","POST"})
     */
    public function note(Plat $plat, Request $request, EntityManagerInterface $em, NoteRepository $noteRepo)
    {
        $user = $this->getUser();
        $note = new Note();
        $form = $this->createForm(NoteType::class,$note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $noteplats = $noteRepo->findBy([
                'plats' => $plat
            ]);
            $notes = [];
            foreach($noteplats as $noteplat){
                $notes[] = $noteplat->getNote();
            }
            $notes[] = $note->getNote();
            if(empty($notes)){
                $noteMoyenne = $note->getNote();
            }else{
                $noteMoyenne = array_sum($notes)/(count($notes));
            }
            $plat->setNoteMoyenne($noteMoyenne);
            $note->setPlats($plat)
                 ->setUser($user);

            $em->persist($note);
            $em->flush();
            return $this->redirectToRoute('profil.order');
        }

        return $this->render('user/newNote.html.twig',[
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }
}
