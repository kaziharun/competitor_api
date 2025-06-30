<?php

declare(strict_types=1);

namespace App\Product\Application\Command;

use App\Product\Application\Message\FetchPricesMessage;
use App\Product\Application\Service\DefaultProductIdsService;
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
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly DefaultProductIdsService $defaultProductIdsService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'product-id',
                InputArgument::OPTIONAL,
                'Product ID to fetch prices for (if not provided, fetches all default products)'
            )
            ->setHelp('This command fetches product prices from external APIs asynchronously.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $productId = $input->getArgument('product-id');

        if ($productId) {
            $this->fetchSingleProduct($productId, $io);
        } else {
            $this->fetchAllDefaultProducts($io);
        }

        return Command::SUCCESS;
    }

    private function fetchSingleProduct(string $productId, SymfonyStyle $io): void
    {
        try {
            $productId = new ProductId($productId);
            $requestId = new RequestId(uniqid('fetch_', true));

            $message = FetchPricesMessage::create($productId, $requestId);
            $this->messageBus->dispatch($message);

            $io->success(sprintf(
                'Price fetch request dispatched for product ID: %s (Request ID: %s)',
                $productId,
                $requestId->getValue()
            ));

            $io->note('The message has been queued. Run "php bin/console messenger:consume async" to process it.');
        } catch (\InvalidArgumentException $e) {
            $io->error('Invalid product ID: '.$e->getMessage());
        } catch (\Exception $e) {
            $io->error('Error dispatching message: '.$e->getMessage());
        }
    }

    private function fetchAllDefaultProducts(SymfonyStyle $io): void
    {
        $io->info('Fetching prices for all default products...');

        foreach ($this->defaultProductIdsService->getDefaultProductIds() as $productId) {
            try {
                $productId = new ProductId($productId);
                $requestId = new RequestId(uniqid('fetch_', true));

                $message = FetchPricesMessage::create($productId, $requestId);
                $this->messageBus->dispatch($message);

                $io->text(sprintf(
                    '✓ Dispatched fetch request for product %s (Request ID: %s)',
                    $productId,
                    $requestId->getValue()
                ));
            } catch (\Exception $e) {
                $io->text(sprintf('✗ Failed to dispatch for product %s: %s', $productId, $e->getMessage()));
            }
        }

        $io->success('All fetch requests have been dispatched to the queue.');
        $io->note('Run "php bin/console messenger:consume async" to process the messages.');
    }
}
