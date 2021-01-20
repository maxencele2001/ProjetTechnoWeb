<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\User;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart.index")
     */
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig',[
            'items'=> $cartService->getFullCart(),
            'total'=> $cartService->getTotal(),
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart.add")
     */
    public function add(Plat $plat, CartService $cartService)
    {
        $cartService->add($plat);
        return $this->redirectToRoute('cart.index');
    }

    /**
     * @Route("/cart/remove/{id}", name="cart.remove")
     */
    public function remove($id,CartService $cartService)
    {
        $cartService->remove($id);
        return $this->redirectToRoute('cart.index');
    }

    /**
     * @Route("/cart/order", name="cart.order")
     */
    public function order(CartService $cartService)
    {
        //$id = $this->get('session')->get('id');
        //$emailUser=$user->getEmail();
        $cartService->order($this->getUser());
        return $this->redirectToRoute('cart.email');
        //return $this->redirectToRoute('cart.email', array('cartService' => $cartService));
    }
    /**
     * @Route("/cart/email", name="cart.email")
     */
    public function email( \Swift_Mailer $mailer){
        $cartService=('test');
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('aurelien.robier@numericable.fr')
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['cartService' => $cartService]
                ),
                'text/html'
            );
                $mailer->send($message);
    
        return $this->redirectToRoute('index');
    }
}
    /*
    public function email( \Swift_Mailer $mailer, User $user){
        $user->$this->getUser();
        $name=$user->getName();
        $emailUser=$user->getEmail();
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo($emailUser)
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['name' => $name]
                ),
                'text/html'
            );
                $mailer->send($message);
    
        return $this->redirectToRoute('index');
    }
    
    public function email( \Swift_Mailer $mailer){
        $name='Hubert';
        //get user 
        // On crée le message
                $message = (new \Swift_Message('Ordre de commande'))
                    // On attribue l'expéditeur
                    ->setFrom('email@envoie.com')
                    // On attribue le destinataire
                    ->setTo('aurelien.robier@numericable.fr')
                    // On crée le texte avec la vue
                    ->setBody(
                        $this->renderView(
                            'emails/registration.html.twig', ['name' => $name ]
                        ),
                        'text/html'
                    )
                ;
                $mailer->send($message);
    
                $this->addFlash('message', 'Votre message a été transmis, nous vous répondrons dans les meilleurs délais.'); // Permet un message flash de renvoi
            
    
        return $this->redirectToRoute('index');
    }
    */