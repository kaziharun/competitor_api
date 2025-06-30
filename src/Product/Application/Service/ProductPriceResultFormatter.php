<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use App\Product\Domain\Entity\ProductPrice;

final class ProductPriceResultFormatter
{
    public function formatAsTable(array $productPrices): string
    {
        if (empty($productPrices)) {
            return 'No products found.';
        }

        $table = "┌─────────────┬──────────────┬──────────┬─────────────────────┬─────────────────────┐\n";
        $table .= "│ Product ID  │ Vendor Name  │ Price    │ Fetched At          │ Created At          │\n";
        $table .= "├─────────────┼──────────────┼──────────┼─────────────────────┼─────────────────────┤\n";

        foreach ($productPrices as $productPrice) {
            $table .= sprintf(
                "│ %-11s │ %-12s │ $%-7.2f │ %-19s │ %-19s │\n",
                $productPrice->getProductId()->getValue(),
                $productPrice->getVendorName()->getValue(),
                $productPrice->getPrice()->getValue(),
                $productPrice->getFetchedAt()->getValue()->format('Y-m-d H:i:s'),
                $productPrice->getCreatedAt()->format('Y-m-d H:i:s')
            );
        }

        $table .= "└─────────────┴──────────────┴──────────┴─────────────────────┴─────────────────────┘\n";

        return $table;
    }

    public function formatAsJson(array $productPrices): string
    {
        $data = [
            'success' => true,
            'data' => array_map(
                fn (ProductPrice $price) => [
                    'product_id' => $price->getProductId()->getValue(),
                    'vendor_name' => $price->getVendorName()->getValue(),
                    'price' => $price->getPrice()->getValue(),
                    'fetched_at' => $price->getFetchedAt()->getValue()->format('Y-m-d H:i:s'),
                    'created_at' => $price->getCreatedAt()->format('Y-m-d H:i:s'),
                ],
                $productPrices
            ),
            'count' => count($productPrices),
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function formatAsSummary(array $productPrices): string
    {
        if (empty($productPrices)) {
            return 'No products found.';
        }

        $lowestPrice = $this->getLowestPrice($productPrices);
        $highestPrice = $this->getHighestPrice($productPrices);
        $averagePrice = $this->getAveragePrice($productPrices);

        return sprintf(
            'Summary: %d products | Lowest: $%.2f | Highest: $%.2f | Average: $%.2f',
            count($productPrices),
            $lowestPrice,
            $highestPrice,
            $averagePrice
        );
    }

    private function getLowestPrice(array $productPrices): float
    {
        if (empty($productPrices)) {
            return 0.0;
        }

        $prices = array_map(fn (ProductPrice $price) => $price->getPrice()->getValue(), $productPrices);

        return min($prices);
    }

    private function getHighestPrice(array $productPrices): float
    {
        if (empty($productPrices)) {
            return 0.0;
        }

        $prices = array_map(fn (ProductPrice $price) => $price->getPrice()->getValue(), $productPrices);

        return max($prices);
    }

    private function getAveragePrice(array $productPrices): float
    {
        if (empty($productPrices)) {
            return 0.0;
        }

        $prices = array_map(fn (ProductPrice $price) => $price->getPrice()->getValue(), $productPrices);

        return array_sum($prices) / count($prices);
    }
}
