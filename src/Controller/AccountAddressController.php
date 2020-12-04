<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    /**
     * @Route("/compte/adresses", name="account_address")
     */
    public function index(): Response
    {
        
        return $this->render('account/address.html.twig');
    }

    /**
     * @Route("/compte/adresses/add", name="account_address_add")
     */
    public function add(Cart $cart, Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address;

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $address->setUser($this->getUser());
            $em->persist($address);
            $em->flush();

            if($cart->get()){

                return $this->redirectToRoute('order');
                
            }else{

                return $this->redirectToRoute('account_address');
            }
             
        }
        
        return $this->render('account/address_form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/adresses/modifier/{id}", name="account_address_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){

            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            $em->flush();

            return $this->redirectToRoute('account_address');
        }
        
        return $this->render('account/address_form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/compte/adresses/supprimer/{id}", name="account_address_delete")
     */
    public function delete(EntityManagerInterface $em, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);

        if($address && $address->getUser() == $this->getUser()){

            $em->remove($address);
            $em->flush();
        }

        return $this->redirectToRoute('account_address');
    }
}
