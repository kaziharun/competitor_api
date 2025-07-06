<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private const API_KEY_HEADER = 'X-API-Key';

    public function __construct(
        private readonly string $apiKey,
    ) {
    }

    public function supports(Request $request): bool
    {
        return $request->headers->has(self::API_KEY_HEADER);
    }

    public function authenticate(Request $request): Passport
    {
        $providedKey = $request->headers->get(self::API_KEY_HEADER);

        if (null === $providedKey) {
            throw new CustomUserMessageAuthenticationException('No API key provided');
        }

        if (!hash_equals($this->apiKey, $providedKey)) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        return new SelfValidatingPassport(
            new UserBadge('api-user', function () {
                return new ApiUser();
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse([
            'error' => 'Authentication failed',
            'message' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }
}
