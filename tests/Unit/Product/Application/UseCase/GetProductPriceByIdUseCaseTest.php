<?php

declare(strict_types=1);

namespace Tests\Unit\App\Product\Application\UseCase;

use App\Product\Application\UseCase\GetProductPriceByIdUseCase;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\Service\ProductPriceCacheServiceInterface;
use App\Product\Domain\ValueObject\ProductId;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\Product\ProductTestCase;

class GetProductPriceByIdUseCaseTest extends ProductTestCase
{
    private MockObject&ProductPriceRepositoryInterface $repositoryMock;
    private MockObject&ProductPriceCacheServiceInterface $cacheServiceMock;
    private GetProductPriceByIdUseCase $useCase;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ProductPriceRepositoryInterface::class);
        $this->cacheServiceMock = $this->createMock(ProductPriceCacheServiceInterface::class);
        $this->useCase = new GetProductPriceByIdUseCase(
            $this->repositoryMock,
            $this->cacheServiceMock
        );
    }

    public function testReturnsCachedPriceWhenAvailable(): void
    {
        $productId = new ProductId('123');
        $cachedPrice = $this->createSampleProductPrice('123');

        $this->cacheServiceMock->expects($this->once())
            ->method('getCachedProductPrice')
            ->with($productId)
            ->willReturn($cachedPrice);

        $result = $this->useCase->execute($productId);

        $this->assertSame($cachedPrice, $result);
        $this->repositoryMock->expects($this->never())->method('findByProductId');
    }

    public function testFetchesFromRepositoryWhenNotCached(): void
    {
        $productId = new ProductId('123');
        $dbPrice = $this->createSampleProductPrice('123');

        $this->cacheServiceMock->expects($this->once())
            ->method('getCachedProductPrice')
            ->with($productId)
            ->willReturn(null);

        $this->repositoryMock->expects($this->once())
            ->method('findByProductId')
            ->with($productId)
            ->willReturn($dbPrice);

        $this->cacheServiceMock->expects($this->once())
            ->method('cacheProductPrice')
            ->with($dbPrice);

        $result = $this->useCase->execute($productId);

        $this->assertSame($dbPrice, $result);
    }

    public function testReturnsNullWhenProductNotFound(): void
    {
        $productId = new ProductId('999');

        $this->cacheServiceMock->expects($this->once())
            ->method('getCachedProductPrice')
            ->with($productId)
            ->willReturn(null);

        $this->repositoryMock->expects($this->once())
            ->method('findByProductId')
            ->with($productId)
            ->willReturn(null);

        $result = $this->useCase->execute($productId);

        $this->assertNull($result);
        $this->cacheServiceMock->expects($this->never())->method('cacheProductPrice');
    }
}
