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
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    protected function getEntityClass(): string
    {
        return ProductPrice::class;
    }

    public function saveOrUpdate(ProductPrice $newPrice): void
    {
        $existingPrice = $this->findByProductId($newPrice->getProductId());

        if ($existingPrice) {
            $this->updatePriceDetails($existingPrice, $newPrice);
        } else {
            $this->entityManager->persist($newPrice);
        }

        $this->entityManager->flush();
    }

    private function updatePriceDetails(ProductPrice $existingPrice, ProductPrice $newPriceData): void
    {
        $existingPrice->updatePrice(
            $newPriceData->getPrice(),
            $newPriceData->getVendorName(),
            $newPriceData->getFetchedAt()
        );
    }

    public function saveAll(array $productPrices): void
    {
        foreach ($productPrices as $productPrice) {
            if ($productPrice instanceof ProductPrice) {
                $this->saveOrUpdate($productPrice);
            } else {
                $this->entityManager->persist($productPrice);
            }
        }
        $this->entityManager->flush();
    }

    public function findByProductId(ProductId $productId): ?ProductPrice
    {
        return $this->entityManager->getRepository(ProductPrice::class)
            ->findOneBy(['productId' => $productId->getValue()]);
    }

    public function findAll(?int $limit = null): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
           ->from(ProductPrice::class, 'p')
           ->orderBy('p.createdAt', 'DESC');

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
