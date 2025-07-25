<?php

declare(strict_types=1);

namespace App\Shared\Presentation\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class ApiUser implements UserInterface
{
    public function getRoles(): array
    {
        return ['ROLE_API'];
    }

    public function getUserIdentifier(): string
    {
        return 'api-user';
    }

    public function eraseCredentials(): void
    {
    }
}
