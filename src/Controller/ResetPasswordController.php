<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        if($this->getUser()){

            return $this->redirectToRoute('home');
        }

        if($request->get('email')){

            $user = $userRepository->findOneByEmail($request->get('email'));

            if($user){
                // 1 : Enregistrer en base la demande de reset_password
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $em->persist($reset_password);
                $em->flush();

                // 2 : Envoie d'un email à l'utilisateur avec un lien lui permettant de réinitialiser son mot de passe
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                    ]);
                $content = "Bonjour".$user->getFirstname()."<br/>Vous avez demandé à réinitialiser votre mot de passe sur le site de la boutique My Shop<br/>";
                $content = "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'>mettre à jour votre mot de passe.</a>";
                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe sur la boutique My Shop', $content);

                $this->addFlash('notice', 'Vous allez recevoir un mail dans quelques secondes avec la procédure pour réinitialiser votre mot de passe.');

            }else{

                $this->addFlash('notice', 'Cette adresse email est inconnue.');

                return $this->redirectToRoute('reset_password');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/mot-de-passe-oublie/{token}", name="update_password")
     */
    public function update(Request $request, $token, ResetPasswordRepository $reset, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $reset_password = $reset->findOneByToken($token);

        if(!$reset_password){

            return $this->redirectToRoute('reset_password');
        }

        //vérifier si le createdAt = now - 1H
        $now = new \DateTime();

        if($now > $reset_password->getCreatedAt()->modify('+ 1 hour')){

            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveler');

            return $this->redirectToRoute('reset_password');
        }

        // Rendre une vue avec nouveau mot de passe 
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
            $new_pwd = $form->get('new_password')->getData();

            //Encodage du mot de passe
            $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);
            $reset_password->getUser()->setPassword($password);

            $em->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
