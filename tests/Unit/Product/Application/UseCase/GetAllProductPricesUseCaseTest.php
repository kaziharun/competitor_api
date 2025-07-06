<?php

declare(strict_types=1);

namespace Tests\Unit\App\Product\Application\UseCase;

use App\Product\Application\UseCase\GetAllProductPricesUseCase;
use App\Product\Domain\Repository\ProductPriceRepositoryInterface;
use App\Product\Domain\Service\ProductPriceCacheServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\Product\ProductTestCase;

class GetAllProductPricesUseCaseTest extends ProductTestCase
{
    private MockObject&ProductPriceRepositoryInterface $repositoryMock;
    private MockObject&ProductPriceCacheServiceInterface $cacheServiceMock;
    private $useCase;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(ProductPriceRepositoryInterface::class);
        $this->cacheServiceMock = $this->createMock(ProductPriceCacheServiceInterface::class);
        $this->useCase = new GetAllProductPricesUseCase(
            $this->repositoryMock,
            $this->cacheServiceMock
        );
    }

    public function testReturnsCachedDataWhenAvailable()
    {
        $cachedData = [['id' => '123', 'price' => 10.99]];

        $this->cacheServiceMock->method('getCachedProductList')
            ->willReturn($cachedData);

        $result = $this->useCase->execute();

        $this->assertEquals($cachedData, $result);
    }

    public function testFetchesFromRepositoryWhenNoCache()
    {
        $productPrice = $this->createSampleProductPrice('123');

        $this->cacheServiceMock->method('getCachedProductList')
            ->willReturn(null);

        $this->repositoryMock->method('findAll')
            ->willReturn([$productPrice]);

        $result = $this->useCase->execute();

        $this->assertEquals([$productPrice], $result);
    }

    public function testCachesDataAfterFetchingFromRepository()
    {
        $productPrice = $this->createSampleProductPrice('123');

        $this->cacheServiceMock->method('getCachedProductList')
            ->willReturn(null);

        $this->repositoryMock->method('findAll')
            ->willReturn([$productPrice]);

        $this->cacheServiceMock->expects($this->once())
            ->method('cacheProductList')
            ->with([$productPrice]);

        $this->useCase->execute();
    }
}
