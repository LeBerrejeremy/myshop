<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function index(UserRepository $userRepository, Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user= new User;
        $form= $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();

            $search_email = $userRepository->findOneByEmail($user->getEmail());

            if(!$search_email){

                $password = $encoder->encodePassword($user,$user->getPassword());
                $user->setPassword($password);


                $em->persist($user);
                $em->flush();

                $mail = new Mail();
                $content = "Bonjour".$user->getFirstname()."<br/>Bienvenue sur la première boutique dédiée au made in France.<br/>Vous trouverez sur notre site des produits réalisés avec soins par des créateurs et avec des matèriaux Francais.<br/>Nous vous souhaitons une agréable visite sur notre site.<br/>Merci pour votre inscription.";
                $mail->send($user->getEmail(), $user->getFirstname(), "Bienvenue sur la boutique My Shop", $content);

                $notification = "Votre inscription c'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte";
            }else{

                $notification = "L'email que vous avez renseigné existe déjà.";
            }
            
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
