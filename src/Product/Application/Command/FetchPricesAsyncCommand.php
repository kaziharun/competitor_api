<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Product\Application\Message\FetchPricesMessage;
use App\Product\Domain\Service\DefaultProductIdsService;
use App\Product\Domain\ValueObject\ProductId;
use App\Product\Domain\ValueObject\RequestId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:fetch-prices-async',
    description: 'Fetch product prices asynchronously from external APIs'
)]
final class FetchPricesAsyncCommand extends Command
{
    private const ARG_PRODUCT_ID = 'product-id';

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly DefaultProductIdsService $productIdsService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::ARG_PRODUCT_ID,
            InputArgument::OPTIONAL,
            'Specific product ID to fetch (default: all products)'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        return $input->getArgument(self::ARG_PRODUCT_ID)
            ? $this->handleSingleProduct($input->getArgument(self::ARG_PRODUCT_ID), $io)
            : $this->handleAllProducts($io);
    }

    private function handleSingleProduct(string $productId, SymfonyStyle $io): int
    {
        try {
            $this->dispatchFetchMessage(new ProductId($productId));
            $io->success('Price fetch queued for product: '.$productId);

            return Command::SUCCESS;
        } catch (\InvalidArgumentException $e) {
            $io->error('Validation error: '.$e->getMessage());

            return Command::INVALID;
        } catch (\Throwable $e) {
            $io->error('Dispatch failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    private function handleAllProducts(SymfonyStyle $io): int
    {
        $io->section('Dispatching price fetches for all products');

        $results = array_map(
            fn (string $id) => $this->processProductId($id, $io),
            $this->productIdsService->getDefaultProductIds()
        );

        $successCount = count(array_filter($results));
        $io->success(sprintf('Queued %d/%d products', $successCount, count($results)));

        return $successCount > 0 ? Command::SUCCESS : Command::FAILURE;
    }

    private function processProductId(string $productId, SymfonyStyle $io): bool
    {
        try {
            $this->dispatchFetchMessage(new ProductId($productId));
            $io->text(" $productId");

            return true;
        } catch (\Throwable $e) {
            $io->text("<error> $productId: {$e->getMessage()}</error>");

            return false;
        }
    }

    private function dispatchFetchMessage(ProductId $productId): void
    {
        $this->messageBus->dispatch(
            FetchPricesMessage::create(
                $productId,
                RequestId::generate()
            )
        );
    }
}
