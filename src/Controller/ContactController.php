<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/nous-contacter", name="contact")
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            
            $data = $form->getData();
            $content = "nom : ".$data['nom']."<br/>prénom : ".$data['prenom']."<br/>email : ".$data['email']."<br/>demande : ".$data['content']."<br/>";
            $mail = new Mail();
            $mail->send("leberrejeremy29@gmail.com", 'My Shop', 'Vous avez reçu une nouvelle demande de contact', $content);



            $this->addFlash('notice', 'Merci de nous avoir contacté. Nore équipe va vous répondre dans les meilleurs délais.');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
