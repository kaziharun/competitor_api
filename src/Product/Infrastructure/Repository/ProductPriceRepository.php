<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Repository;

use App\Product\Domain\Entity\ProductPrice;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\ValueObject\ProductId;
use App\Shared\Infrastructure\Persistence\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ProductPriceRepository extends BaseRepository implements ProductPriceRepositoryInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    protected function getEntityClass(): string
    {
        return ProductPrice::class;
    }

    public function save(\App\Shared\Domain\Entity\BaseEntity $entity): void
    {
        if (!$entity instanceof ProductPrice) {
            throw new \InvalidArgumentException('Entity must be a ProductPrice');
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function saveAll(array $productPrices): void
    {
        foreach ($productPrices as $productPrice) {
            $this->entityManager->persist($productPrice);
        }
        $this->entityManager->flush();
    }

    public function findByProductId(ProductId $productId): ?ProductPrice
    {
        return $this->entityManager->getRepository(ProductPrice::class)
            ->findOneBy(['productId' => $productId->getValue()]);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(ProductPrice::class)->findAll();
    }

    public function deleteByProductId(ProductId $productId): void
    {
        $productPrice = $this->findByProductId($productId);
        if (null !== $productPrice) {
            $this->entityManager->remove($productPrice);
            $this->entityManager->flush();
        }
    }
}
