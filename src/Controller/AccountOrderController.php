<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    /**
     * @Route("/compte/commande", name="account_order")
     */
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findSuccessOrders($this->getUser());

        return $this->render('account/order.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/compte/commande/{reference}", name="account_order_show")
     */
    public function show(OrderRepository $orderRepository, $reference): Response
    {
        $order = $orderRepository->findOneByReference($reference);

        if(!$order || $order->getUser() != $this->getUser()){

            return $this->redirectToRoute('account_order');
        }
        
        return $this->render('account/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
