<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

final class PriceData
{
    public function __construct(
        private readonly VendorName $vendor,
        private readonly Price $price,
    ) {
    }

    /**
     * @param array{vendor: string, price: float} $data
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['vendor']) || !isset($data['price'])) {
            throw new \InvalidArgumentException('Price data must contain vendor and price');
        }

        return new self(
            new VendorName($data['vendor']),
            new Price((float) $data['price'])
        );
    }

    public function getVendor(): VendorName
    {
        return $this->vendor;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return array{vendor: string, price: float}
     */
    public function toArray(): array
    {
        return [
            'vendor' => $this->vendor->getValue(),
            'price' => $this->price->getValue(),
        ];
    }

    public function equals(PriceData $other): bool
    {
        return $this->vendor->equals($other->vendor) && $this->price->equals($other->price);
    }
}
