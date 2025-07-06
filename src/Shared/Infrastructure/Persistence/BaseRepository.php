<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Entity\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

abstract class BaseRepository
{
    protected ObjectRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->repository = $this->entityManager->getRepository($this->getEntityClass());
    }

    abstract protected function getEntityClass(): string;

    public function findById(int $id): ?BaseEntity
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function count(): int
    {
        return (int) $this->entityManager
            ->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from($this->getEntityClass(), 'e')
            ->getQuery()
            ->getSingleScalarResult();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    protected function getRepository(): ObjectRepository
    {
        return $this->repository;
    }
}
