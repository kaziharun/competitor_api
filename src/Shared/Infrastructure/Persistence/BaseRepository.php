<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Entity\BaseEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of BaseEntity
 */
abstract class BaseRepository
{
    protected EntityManagerInterface $entityManager;
    /**
     * @var ObjectRepository<T>
     */
    protected ObjectRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($this->getEntityClass());
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getEntityClass(): string;

    /**
     * @param array<T> $entities
     */
    public function saveAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->save($entity);
        }
    }

    public function save(BaseEntity $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function remove(BaseEntity $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?BaseEntity
    {
        return $this->repository->find($id);
    }

    /**
     * @return array<T>
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function count(): int
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(e.id)')
           ->from($this->getEntityClass(), 'e');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return ObjectRepository<T>
     */
    protected function getRepository(): ObjectRepository
    {
        return $this->repository;
    }
}
