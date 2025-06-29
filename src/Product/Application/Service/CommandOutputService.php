<?php

declare(strict_types=1);

namespace App\Product\Application\Service;

use Symfony\Component\Console\Style\SymfonyStyle;

final class CommandOutputService implements CommandOutputInterface
{
    public function __construct(
        private readonly SymfonyStyle $io,
        private readonly ProductPriceResultFormatter $formatter,
    ) {
    }

    public function displaySingleProductFetch(string $productId, bool $isDryRun): void
    {
        $this->io->title('Fetching Prices for Product: '.$productId);

        if ($isDryRun) {
            $this->io->note('DRY RUN MODE - No actual fetching will occur');
            $this->io->table(['Product ID'], [[$productId]]);
            $this->io->success('Dry run completed - would fetch prices for product: '.$productId);

            return;
        }
    }

    public function displaySingleProductSuccess(string $productId): void
    {
        $this->io->success('Successfully fetched and aggregated prices for product: '.$productId);
    }

    public function displaySingleProductError(string $productId, string $errorMessage): void
    {
        $this->io->error('Failed to fetch prices for product '.$productId.': '.$errorMessage);
    }

    public function displayMultipleProductsStart(array $productIds, bool $isDryRun): void
    {
        $this->io->title('Fetching Prices for All Products');
        $this->io->text(sprintf('Found %d products to process', count($productIds)));

        if ($isDryRun) {
            $this->io->note('DRY RUN MODE - No actual fetching will occur');
            $this->io->table(['Product ID'], array_map(fn ($id) => [$id], $productIds));
            $this->io->success('Dry run completed - would fetch prices for '.count($productIds).' products');
        }
    }

    public function displayProgressBar(int $total): void
    {
        $progressBar = $this->io->createProgressBar($total);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->start();
    }

    public function displaySummary(FetchResultCollection $results): void
    {
        $formatted = $this->formatter->formatCollection($results);
        $this->io->section('Summary');
        $this->io->table(
            ['Metric', 'Count'],
            [
                ['Total Products', $formatted['summary']['total']],
                ['Successful', $formatted['summary']['successful']],
                ['Failed', $formatted['summary']['failed']],
            ]
        );

        $this->io->section('Results');
        $this->io->table(
            ['Product ID', 'Status', 'Error'],
            array_map(
                fn ($row) => [$row['productId'], $row['status'], $row['error'] ?? ''],
                $formatted['results']
            )
        );
    }

    public function displayError(string $message): void
    {
        $this->io->error($message);
    }

    public function displayNoProductsError(): void
    {
        $this->io->error('No product IDs provided');
    }
}
