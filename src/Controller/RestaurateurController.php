<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Form\PlatType;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
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
    /**
     * @Route("/myrestaurants", name="restaurateur.index")
     */
    public function index(RestaurantRepository $repo): Response
    {
        $restaurants = $repo->getByIdUser($this->getUser());
        return $this->render('restaurateur/restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * @Route("/new", name="restaurateur.new", methods={"GET","POST"})
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
        
            return $this->redirectToRoute('restaurateur.index');
        }

        return $this->render('restaurateur/restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/myrestaurants/{id}", name="restaurateur.restaurant")
     */
    public function oneRestaurant(Restaurant $restaurant)//rajouter un if ou genre on verif si ce resto appartient bien au user
    {
        return $this->render('restaurateur/restaurant/restaurant.html.twig',[
            'restaurant' => $restaurant
        ]);
    }

    /**
     * @Route("/myrestaurants/{id}/newplat", name="restaurateur.newplat")
     */
    public function newPlat(Request $request, EntityManagerInterface $em, Restaurant $restaurant)
    {
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
            
            return $this->redirectToRoute('restaurateur.index');//changer et mettre la liste des plats
        }

        return $this->render('restaurateur/plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }
}
