<?php

declare(strict_types=1);

namespace App\Product\Application\MessageHandler;

use App\Product\Application\Message\FetchPricesMessage;
use App\Product\Application\UseCase\FetchCompetitorPricesUseCase;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
final class FetchPricesMessageHandler
{
    public function __construct(
        private readonly FetchCompetitorPricesUseCase $fetchUseCase,
        private readonly ProductPriceRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FetchPricesMessage $message): void
    {
        $productId = $message->getProductId();

        try {
            $prices = $this->fetchUseCase->execute($productId);

            if (!empty($prices)) {
                $this->repository->saveAll($prices);
            }
        } catch (\InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
