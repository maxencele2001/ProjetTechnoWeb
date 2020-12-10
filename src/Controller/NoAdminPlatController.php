<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Repository\PlatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoAdminPlatController extends AbstractController
{
    /**
     * @Route("/plat", name="plat")
     */
    public function index(PlatRepository $repo): Response
    {
        $plats = $repo->findAll();
        return $this->render('plat/index.html.twig', [
            'plats' => $plats,
        ]);
    }

    public function showOne(Plat $plat)
    {
        
    }
}
