<?php

declare(strict_types=1);

namespace App\Product\Domain\Entity;

use App\Product\Domain\ValueObject\FetchedAt;
use App\Product\Domain\ValueObject\Price;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\VendorName;
use App\Shared\Domain\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_prices')]
#[ORM\Index(columns: ['product_id'])]
#[ORM\Index(columns: ['fetched_at'])]
class ProductPrice extends BaseEntity
{
    #[ORM\Column(length: 255)]
    private string $productId;

    #[ORM\Column(length: 255)]
    private string $vendorName;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $fetchedAt;

    public function __construct(
        ProductId $productId,
        VendorName $vendorName,
        Price $price,
        FetchedAt $fetchedAt,
    ) {
        parent::__construct();
        $this->productId = $productId->getValue();
        $this->vendorName = $vendorName->getValue();
        $this->price = (string) $price->getValue();
        $this->fetchedAt = $fetchedAt->getValue();
    }

    public function getProductId(): ProductId
    {
        return new ProductId($this->productId);
    }

    public function getVendorName(): VendorName
    {
        return new VendorName($this->vendorName);
    }

    public function getPrice(): Price
    {
        return new Price((float) $this->price);
    }

    public function getFetchedAt(): FetchedAt
    {
        return new FetchedAt($this->fetchedAt);
    }

    public function getIdentifier(): ProductId
    {
        return new ProductId($this->productId);
    }

    public function updatePrice(Price $newPrice, VendorName $newVendor, FetchedAt $newFetchedAt): void
    {
        $this->price = (string) $newPrice->getValue();
        $this->vendorName = $newVendor->getValue();
        $this->fetchedAt = $newFetchedAt->getValue();
        $this->updateTimestamp();
    }

    public function equals(ProductPrice $other): bool
    {
        return $this->productId === $other->productId
               && $this->vendorName === $other->vendorName
               && $this->price === $other->price
               && $this->fetchedAt->getTimestamp() === $other->fetchedAt->getTimestamp();
    }
}
