<?php

namespace App\Controller;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class AuthController extends AbstractController
{
    #[Route('/register', name: 'app_auth', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EventDispatcherInterface $eventDispatcher, TokenStorageInterface $tokenStorage): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setRoles(['ROLE_USER']);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => 'Validation failed', 'errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($token);

        $event = new InteractiveLoginEvent($request, $token);
        $eventDispatcher->dispatch($event, SecurityEvents::INTERACTIVE_LOGIN);

        return $this->json([
            'message' => 'User registered successfully',
        ]);
    }   


}
