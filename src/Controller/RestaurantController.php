<?php

namespace App\Controller;

use DateTime;
use App\Entity\Plat;
use App\Repository\PlatRepository;
use App\Entity\Restaurant;
use App\Entity\User;
use App\Form\RestaurantType;
use App\Repository\NoteRepository;
use App\Repository\RestaurantRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/restaurant")
 */
class RestaurantController extends AbstractController
{
    
    /**
     * @Route("/all", name="restaurant_index", methods={"GET"})
     */
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        //$types = $tyrepo->findBy(array('id' => $restaurant->getType()));
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
            //'types' => $types,
        ]);
    }

    /**
     * @Route("/new", name="restaurant_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile */
            $file = $form->get('logoFile')->getData();

            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('upload_directory'),
                $filename
            );

            $restaurant->setLogo($filename);
            $em->persist($restaurant);
            $em->flush();

            return $this->redirectToRoute('restaurant_index');
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurant_show", methods={"GET"}) 
     */
    public function show(Restaurant $restaurant, PlatRepository $repoPlat,  OrderRepository $orderRepo, RestaurantRepository $repo): Response
    {
        $orders = $this->PREFABorderList($repo,$orderRepo, $restaurant);
        $orderEncours = $orders[0];
        $orderLivre = $orders[1];
        $plats = $repoPlat->getByID($restaurant->getId());
        return $this->render('restaurant/show.html.twig', [
            'plats' => $plats,
            'restaurant'=>$restaurant,
            'orderEncours' => $orderEncours,
            'orderLivre' => $orderLivre,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="restaurant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile */
            $file = $form->get('logoFile')->getData();

            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('upload_directory'),
                $filename
            );

            $restaurant->setLogo($filename);
            $em->persist($restaurant);
            $em->flush();

            return $this->redirectToRoute('restaurant_index');
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Restaurant $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('restaurant_index');
    }
    ##################### Commandes ORDER #############################
    public function PREFABorderList(RestaurantRepository $repo, OrderRepository $orderRepo, Restaurant $restaurant)
    {
        $restaurants = $repo->find($restaurant);
        $orderEncours = [];
        $orderLivre = [];
        $resto = $repo->find($restaurants);
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
        
        $orders = [$orderEncours,$orderLivre];
    
        return $orders;
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
     * @Route("/{id}/order/note", name="resto.order.progress", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function Avis(NoteRepository $noteRepo)
    {
        $notes = $noteRepo->findAll();  
        return $this->render('restaurateur/noteList.html.twig', [
            'notes' => $notes,
        ]);

    }

    
}
