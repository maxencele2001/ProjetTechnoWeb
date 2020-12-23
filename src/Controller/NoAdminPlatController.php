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
     * @Route("/plats", name="plat")
     */
    public function index(PlatRepository $repo): Response
    {
        $plats = $repo->findAll();
        return $this->render('no_admin_plat/index.html.twig', [
            'plats' => $plats,
        ]);
    }

    /**
     * @Route("/plats/{id}", name="plat.show")
     */
    public function showOne(Plat $plat)
    {
        return $this->render('no_admin_plat/index.html.twig', [
            'plat' => $plat,
        ]);
    }
}
