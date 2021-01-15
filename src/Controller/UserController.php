<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
