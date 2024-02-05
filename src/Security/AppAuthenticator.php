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
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppAuthenticator extends AbstractAuthenticator
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userProvider = $userProvider;
        $this->userPasswordHasher = $userPasswordHasher;
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

        $user = $this->userProvider->loadUserByIdentifier($username);

        if (!$this->userPasswordHasher->isPasswordValid($user, $password)) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
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
        // Return a response indicating authentication failure
        return new JsonResponse(['error' => 'Authentication failed.'], Response::HTTP_UNAUTHORIZED);
    }
}
