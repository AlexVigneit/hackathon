<?php

namespace App\Controller;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;


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
            $errorsMessages = [];
            foreach ($errors as $error) {
            $errorsMessages[] = $error->getMessage();
            }
            $errorsString = implode('<br>', $errorsMessages);

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
    
    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        $security->logout();
    }


}
