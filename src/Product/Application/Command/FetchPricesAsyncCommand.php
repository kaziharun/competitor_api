<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Product\Application\Service\CommandOutputInterface;
use App\Product\Application\Service\CommandOutputService;
use App\Product\Application\Service\ProductFetchServiceInterface;
use App\Product\Application\Service\ProductPriceResultFormatter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fetch-prices-async',
    description: 'Fetch and aggregate prices for products from external APIs asynchronously'
)]
final class FetchPricesAsyncCommand extends Command
{
    public function __construct(
        private readonly ProductFetchServiceInterface $productFetchService,
        private readonly ProductPriceResultFormatter $formatter,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'product-id',
                InputArgument::OPTIONAL,
                'The product ID to fetch prices for (if not provided, fetches all products)'
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Fetch prices for all products (same as not providing product-id)'
            )
            ->addOption(
                'products',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Comma-separated list of product IDs to fetch (overrides default list)',
                implode(',', $this->productFetchService->getDefaultProductIds())
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Show what would be fetched without actually fetching'
            )
            ->setHelp('This command fetches prices from external APIs and stores the lowest price for a product or all products.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $outputService = new CommandOutputService($io, $this->formatter);

        $productId = $input->getArgument('product-id');
        $fetchAll = $input->getOption('all');
        $productsOption = $input->getOption('products');
        $isDryRun = $input->getOption('dry-run');

        // If no product ID is provided or --all flag is used, fetch all products
        if (null === $productId || $fetchAll) {
            return $this->executeMultipleProductsFetch($outputService, $productsOption, $isDryRun);
        }

        // Fetch single product
        return $this->executeSingleProductFetch($outputService, $productId, $isDryRun);
    }

    private function executeSingleProductFetch(CommandOutputInterface $outputService, string $productId, bool $isDryRun): int
    {
        $outputService->displaySingleProductFetch($productId, $isDryRun);

        if ($isDryRun) {
            return Command::SUCCESS;
        }

        $result = $this->productFetchService->fetchSingleProduct($productId);

        if ($result->isSuccess()) {
            $outputService->displaySingleProductSuccess($productId);

            return Command::SUCCESS;
        }

        $outputService->displaySingleProductError($productId, $result->getErrorMessage() ?? 'Unknown error');

        return Command::FAILURE;
    }

    private function executeMultipleProductsFetch(CommandOutputInterface $outputService, string $productsOption, bool $isDryRun): int
    {
        $productIds = $this->productFetchService->parseProductIds($productsOption);

        if (empty($productIds)) {
            $outputService->displayNoProductsError();

            return Command::FAILURE;
        }

        $outputService->displayMultipleProductsStart($productIds, $isDryRun);

        if ($isDryRun) {
            return Command::SUCCESS;
        }

        $results = $this->productFetchService->fetchMultipleProducts($productIds);
        $outputService->displaySummary($results);

        return $results->isAllSuccessful() ? Command::SUCCESS : Command::FAILURE;
    }
}
