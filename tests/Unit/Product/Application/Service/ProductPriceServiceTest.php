<?php

declare(strict_types=1);

namespace Tests\Unit\Product\Application\Service;

use App\Product\Application\DTO\Request\GetProductPriceByIdRequestDto;
use App\Product\Application\DTO\Response\ProductPriceResponseDto;
use App\Product\Application\Service\ProductPriceService;
use App\Product\Application\UseCase\GetAllProductPricesUseCase;
use App\Product\Application\UseCase\GetProductPriceByIdUseCase;
use App\Product\Domain\ValueObject\ProductId;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\Unit\Product\ProductTestCase;

class ProductPriceServiceTest extends ProductTestCase
{
    private MockObject&GetAllProductPricesUseCase $getAllUseCaseMock;
    private MockObject&GetProductPriceByIdUseCase $getByIdUseCaseMock;
    private MockObject&ValidatorInterface $validatorMock;
    private ProductPriceService $service;

    protected function setUp(): void
    {
        $this->getAllUseCaseMock = $this->createMock(GetAllProductPricesUseCase::class);
        $this->getByIdUseCaseMock = $this->createMock(GetProductPriceByIdUseCase::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);

        $this->service = new ProductPriceService(
            $this->getAllUseCaseMock,
            $this->getByIdUseCaseMock,
            $this->validatorMock
        );
    }

    public function testGetAllPricesReturnsFormattedResponse(): void
    {
        $productPrice = $this->createSampleProductPrice();

        $this->getAllUseCaseMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn([$productPrice]);

        $result = $this->service->getAllPrices();

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['count']);
        $this->assertIsArray($result['data']);
        $this->assertEquals(
            ProductPriceResponseDto::fromEntity($productPrice)->toArray(),
            $result['data'][0]
        );
    }

    public function testGetAllPricesException(): void
    {
        $this->getAllUseCaseMock
            ->expects($this->once())
            ->method('execute')
            ->willThrowException(new \RuntimeException('Database error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to fetch all product prices');

        $this->service->getAllPrices();
    }

    public function testGetPriceByIdSuccess(): void
    {
        $validProductId = 'valid-123';
        $productPrice = $this->createSampleProductPrice($validProductId);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with(
                $this->callback(function ($subject) use ($validProductId) {
                    return $subject instanceof GetProductPriceByIdRequestDto
                        && $subject->getProductIdString() === $validProductId;
                })
            )
            ->willReturn(new ConstraintViolationList());

        $this->getByIdUseCaseMock->expects($this->once())
            ->method('execute')
            ->with($this->callback(function ($productId) use ($validProductId) {
                return $productId instanceof ProductId
                    && $productId->getValue() === $validProductId;
            }))
            ->willReturn($productPrice);

        $result = $this->service->getPriceById($validProductId);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals(
            ProductPriceResponseDto::fromEntity($productPrice)->toArray(),
            $result['data']
        );
    }

    public function testGetPriceByIdInvalidId(): void
    {
        $invalidId = '123 4';

        $violation = new ConstraintViolation(
            'Invalid product ID format',
            null,
            [],
            null,
            'productId',
            $invalidId
        );

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList([$violation]));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid product ID format');

        $this->service->getPriceById($invalidId);
    }

    public function testGetPriceByIdNotFound(): void
    {
        $validProductId = 'valid-123-id';

        $this->validatorMock
            ->expects($this->once())
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->getByIdUseCaseMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessageMatches("/Product with ID '{$validProductId}' not found/");

        $this->service->getPriceById($validProductId);
    }
}
