<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Form\PlatType;
use App\Form\RestaurantType;
use App\Repository\OrderRepository;
use App\Repository\PlatRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/restaurateur")
 */
class RestaurateurController extends AbstractController
{
    

    ##################### Commandes #############################

    public function PREFABorderList(RestaurantRepository $repo, OrderRepository $orderRepo)
    {
        $restaurants = $repo->getByIdUser($this->getUser());
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
     * @Route("/myrestaurants/{id}/orders", name="restaurateur.orders", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function orderHistory(Restaurant $restaurant, RestaurantRepository $repo, OrderRepository $orderRepo): Response
    {
        $orders = $this->PREFABorderList($repo,$orderRepo, $restaurant);
        $orderEncours = $orders[0];
        $orderLivre = $orders[1];
        return $this->render('restaurateur/orderList.html.twig', [
            'restaurant' =>$restaurant,
            'orderEncours' => $orderEncours,
            'orderLivre' => $orderLivre,
            
        ]);

    }
   
      

    ##################### Restaurant #############################

    /**
     * @Route("/myrestaurants", name="restaurateur.restaurant.all", methods={"GET"})
     */
    public function allRestaurants(RestaurantRepository $repo): Response
    {
        $restaurants = $repo->getByIdUser($this->getUser());
        return $this->render('restaurateur/restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * @Route("/myrestaurants/{id}", name="restaurateur.restaurant.one", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function oneRestaurant(Restaurant $restaurant, PlatRepository $repo)//rajouter un if ou genre on verif si ce resto appartient bien au user
    {
        $this->denyAccessUnlessGranted('SHOW', $restaurant);
        $plats = $repo->getByID($restaurant->getId());
        return $this->render('restaurateur/restaurant/restaurant.html.twig',[
            'restaurant' => $restaurant,
            'plats' => $plats,
        ]);
    }

  

    #-----------------CRUD--------------#

    /**
     * @Route("/myrestaurants/new", name="restaurateur.restaurant.new", methods={"GET","POST"})
     */
    public function newRestaurant(Request $request, EntityManagerInterface $em): Response
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

            $restaurant->setUser($this->getUser());
            $restaurant->setLogo($filename);
            $em->persist($restaurant);
            $em->flush();
        
            return $this->redirectToRoute('restaurateur.restaurant.all');
        }

        return $this->render('restaurateur/restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/myrestaurants/{id}/edit", name="restaurateur.restaurant.edit", methods={"GET","POST"})
     */
    public function editRestaurant(Request $request, Restaurant $restaurant, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $restaurant);
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

            return $this->redirectToRoute('restaurateur.restaurant.all');
        }

        return $this->render('restaurateur/restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/myrestaurants/{id}", name="restaurateur.restaurant.delete", methods={"DELETE"})
     */
    public function deleteRestaurant(Request $request, Restaurant $restaurant): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $restaurant);
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('restaurateur.restaurant.all');
    }

    ###############################PLAT#################################################

    

    /**
     * @Route("/myrestaurants/{restaurant}/plats/{plat}", name="restaurateur.plat.one", methods={"GET"}, requirements={"plat"="\d+"})
     */
    public function onePlat(Restaurant $restaurant,Plat $plat)
    {
        $this->denyAccessUnlessGranted('SHOW', $restaurant); 
        $plats = $restaurant->getPlats();
        $verif = false;
        foreach($plats as $oneplat)
        {
            if($oneplat == $plat)
            {
                $verif = true;
            }
        }
        if($verif)
        {
            return $this->render('restaurateur/plat/plat.html.twig',[
            'restaurant' => $restaurant,
            'plat' => $plat
            ]);
        }else{
            return $this->redirectToRoute('restaurateur.restaurant.one', ['id' => $restaurant->getId()]);
        }
    }

    #----------------------CRUD--------------------------#

    /**
     * @Route("/myrestaurants/{restaurant}/plats/newplat", name="restaurateur.plat.new", methods={"GET","POST"})
     */
    public function newPlat(Request $request, EntityManagerInterface $em, Restaurant $restaurant)
    {
        $this->denyAccessUnlessGranted('NEWPLAT', $restaurant); 
        $idRestaurant = $restaurant->getId();
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile */
            $file = $form->get('imageFile')->getData();

            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('upload_directory'),
                $filename
            );


            $plat->setRestaurants($restaurant);
            $plat->setImage($filename);
            $em->persist($plat);
            $em->flush();
            
            return $this->redirectToRoute('restaurateur.restaurant.one',['id' => $idRestaurant]);//changer et mettre la liste des plats
        }

        return $this->render('restaurateur/plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
            'idRestaurant' => $idRestaurant
        ]);
    }

    /**
     * @Route("/myrestaurants/{restaurant}/plats/{plat}/edit", name="restaurateur.plat.edit", methods={"GET","POST"})
     */
    public function editPlat(Request $request, Restaurant $restaurant, Plat $plat,EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('SHOW', $restaurant); 
        $plats = $restaurant->getPlats();
        $verif = false;
        foreach($plats as $oneplat)
        {
            if($oneplat == $plat)
            {
                $verif = true;
            }
        }
        if($verif)
        {
            $idRestaurant = $restaurant->getId();
            $idPlat = $plat->getId();
            $form = $this->createForm(PlatType::class, $plat);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile */
                $file = $form->get('imageFile')->getData();

                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $filename
                );

                $plat->setImage($filename);
                $em->persist($plat);
                $em->flush();

                return $this->redirectToRoute('restaurateur.restaurant.one',['id' => $idRestaurant]);
            }

            return $this->render('restaurateur/plat/edit.html.twig', [
                'plat' => $plat,
                'form' => $form->createView(),
                'idRestaurant' => $idRestaurant,
                'idPlat' => $idPlat
            ]);
            }else{
                return $this->redirectToRoute('restaurateur.restaurant.one', ['id' => $restaurant->getId()]);
            }
    }

    /**
     * @Route("/myrestaurants/{id}/plats/{plat}", name="restaurateur.plat.delete", methods={"DELETE"})
     */
    public function deletePlat(Request $request,Restaurant $restaurant, Plat $plat): Response
    {
        $this->denyAccessUnlessGranted('SHOW', $restaurant);
        $plats = $restaurant->getPlats();
        $verif = false;
        foreach($plats as $oneplat)
        {
            if($oneplat == $plat)
            {
                $verif = true;
            }
        }
        if($verif)
        {
            $idRestaurant = $restaurant->getId();
            if ($this->isCsrfTokenValid('delete'.$plat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($plat);
            $entityManager->flush();
            }
            return $this->redirectToRoute('restaurateur.restaurant.one',['id' => $idRestaurant]);
        }else{
            return $this->redirectToRoute('restaurateur.restaurant.one', ['id' => $restaurant->getId()]);
        }
    }
}
