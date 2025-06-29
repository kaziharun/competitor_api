<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

interface CommandOutputInterface
{
    public function displaySingleProductFetch(string $productId, bool $isDryRun): void;

    public function displaySingleProductSuccess(string $productId): void;

    public function displaySingleProductError(string $productId, string $errorMessage): void;

    /**
     * Display start message for multiple products fetch.
     *
     * @param array<string> $productIds Array of product IDs
     */
    public function displayMultipleProductsStart(array $productIds, bool $isDryRun): void;

    public function displayProgressBar(int $total): void;

    public function displaySummary(FetchResultCollection $results): void;

    public function displayError(string $message): void;

    public function displayNoProductsError(): void;
}
