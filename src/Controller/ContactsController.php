<?php

namespace App\Controller;

use App\Form\ContactsType;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactsController extends AbstractController
{
    #[Route('/contacts', name: 'app_contacts')]
    public function index(Request $request, Mailer $mailer): Response
    {

        $form = $this->createForm(ContactsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //dd($data);

            $mailer->sendContactsMessage($data);
            $this->addFlash('success', 'Your message has been sent!');
            return $this->redirectToRoute('app_contacts');
        }

        return $this->render('contacts/index.html.twig', [
            'contactsForm' => $form,
        ]);
    }
}
