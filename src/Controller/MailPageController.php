<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailPageController extends AbstractController
{
    #[Route('/mailPage', name: 'mailPage')]
    public function mailPage(): Response
    {
        return $this->render('mail/index.html.twig', [
            'controller_name' => 'MailPageController',
        ]);
    }

    #[Route('/sendMail', name: 'sendMail', method:'POST')]
    public function sendMail(Request $request, Security $security): Response
    {
        $data = json_decode($request->getContent());
        $transport = Transport::fromDsn('smtp://ifullteam@gmail.com:uxjwrfkegiuqxqan@smtp.gmail.com:587');
        $mailer = new Mailer($transport);

        $email = (new Email())
            ->from('ifullteam@gmail.com')
            ->to($security->getUser()->getUserIdentifier())
            ->subject('Rapport d\'analyse github')
            ->html($data['report']);
        try {
            $mailer->send($email);
            return new Response('Email envoyé avec succès au ' . $security->getUser()->getUserIdentifier() . '!');
        } catch (TransportExceptionInterface $e) {
            return new Response('Erreur lors de l\'envoi de l\'email: '.$e->getMessage());
        }
    }
}
