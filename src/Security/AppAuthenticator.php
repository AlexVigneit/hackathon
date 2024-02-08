<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AppAuthenticator extends AbstractAuthenticator
{
    private $userProvider;
    private $userPasswordHasher;
    private $validator;

    public function __construct(UserProviderInterface $userProvider, UserPasswordHasherInterface $userPasswordHasher, ValidatorInterface $validator)
    {
        $this->userProvider = $userProvider;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->validator = $validator;
    }

    public function supports(Request $request): ?bool
    {
        // Assume we're supporting all POST requests to a specific route
        return $request->isMethod('POST') && $request->getPathInfo() === '/login';
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = json_decode($request->getContent(), true);
        $username = $credentials['username'] ?? '';
        $password = $credentials['password'] ?? '';

        // Valider le mot de passe et le format de l'email
        $errors = $this->validator->validate($credentials, new Assert\Collection([
            'username' => [new Assert\Email()],
            'password' => [new Assert\Length(['min' => 8])]
        ]));

        if (count($errors) > 0) {
            // Gérer les erreurs de validation
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new CustomUserMessageAuthenticationException(implode("\n", $errorMessages));
        }

        $user = $this->userProvider->loadUserByIdentifier($username);

        if (!$this->userPasswordHasher->isPasswordValid($user, $password)) {
            $errorMessage = 'Invalid credentials';
            throw new CustomUserMessageAuthenticationException($errorMessage);
        }

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirect or return a response after successful authentication
        return new JsonResponse(['message' => 'Success!'], Response::HTTP_OK);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Récupérer le message d'erreur personnalisé
        $errorMessage = $exception->getMessageKey();

        // Retourner la réponse d'erreur avec le message personnalisé
        return new JsonResponse(['error' => 'Authentication failed.', 'message' => $errorMessage], Response::HTTP_UNAUTHORIZED);
    }
}
