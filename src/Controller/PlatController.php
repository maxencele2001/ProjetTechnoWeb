<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/plat")
 */
class PlatController extends AbstractController
{
    /**
     * @Route("/", name="plat_index", methods={"GET"})
     */
    public function index(PlatRepository $platRepository): Response
    {
        return $this->render('plat/index.html.twig', [
            'plats' => $platRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="plat_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $em): Response
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

            $plat->setImage($filename);
            $em->persist($plat);
            $em->flush();
            
            return $this->redirectToRoute('plat_index');
        }

        return $this->render('plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plat_show", methods={"GET"})
     */
    public function show(Plat $plat): Response
    {
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="plat_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Plat $plat,EntityManagerInterface $em): Response
    {
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

            return $this->redirectToRoute('plat_index');
        }

        return $this->render('plat/edit.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="plat_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Plat $plat): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plat_index');
    }
}
