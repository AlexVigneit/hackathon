<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class MailPageController extends AbstractController
{
    #[Route('/mailPage', name: 'mailPage')]
    public function mailPage(): Response
    {
        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailPageController',
        ]);
    }

    #[Route('/sendMail', name: 'sendMail')]
    public function sendMail(Security $security): Response
    {
        $transport = Transport::fromDsn('smtp://ifullteam@gmail.com:uxjwrfkegiuqxqan@smtp.gmail.com:587');
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('ifullteam@gmail.com')
            ->to($security->getUser()->getUserIdentifier())
            ->subject('Rapport d\'analyse github')
            ->text('The text')
            ->html('<h1>TESTINGZZ</h1>');

        try {
            $mailer->send($email);
            return new Response('Email envoyé avec succès!');
        } catch (TransportExceptionInterface $e) {
            return new Response('Erreur lors de l\'envoi de l\'email: '.$e->getMessage());
        }
    }
}
