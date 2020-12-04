<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, EntityManagerInterface $em, OrderRepository $orderRepository, $stripeSessionId): Response
    {
        $order = $orderRepository->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){

            return $this->redirectToRoute('home');
        }

        if( $order->getState() == 0){

            $cart->remove();
    
            $order->setState(1);
            $em->flush();

            $mail = new Mail();
            $content = "Bonjour".$order->getUser()->getFirstname()."<br/>Merci pour votre commande sur la boutique My Shop.<br/>";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande My Shop est bien validée.", $content);
        }

        return $this->render('order_success/index.html.twig',[
            'order'=> $order
        ]);
    }
}
