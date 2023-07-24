<?php
declare(strict_types = 1);

namespace App\Tests\Unit\Serivce\Product;

use App\DTO\Product\PurchaseDataDTO;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Exception\CalculateCostNegativeValueException;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\Product\CalculateCostService;
use App\Service\TaxNumber\TaxCountryHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CalculateCostServiceTest extends TestCase
{
    private MockObject|ProductRepository $productRepositoryMock;
    private CouponRepository|MockObject $couponRepositoryMock;
    private CalculateCostService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepositoryMock = $this->createMock(ProductRepository::class);
        $this->couponRepositoryMock = $this->createMock(CouponRepository::class);
        $this->service = new CalculateCostService(
            $this->productRepositoryMock,
            $this->couponRepositoryMock,
            new TaxCountryHelper(),
        );
    }

    /**
     * @dataProvider calculateByProductPriceDTOProvider
     */
    public function testCalculateByProductPriceDTO(Product $product, ?Coupon $coupon, string $taxNumber, int $result): void
    {
        $productPriceDTO = new PurchaseDataDTO();
        $productPriceDTO
            ->setProduct('1')
            ->setCouponCode($coupon ? 'code' : null)
            ->setTaxNumber($taxNumber);

        $this->productRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with($productPriceDTO->getProduct())
            ->willReturn($product);

        if (null !== $coupon) {
            $this->couponRepositoryMock
                ->expects(self::once())
                ->method('findOneByCode')
                ->with($productPriceDTO->getCouponCode())
                ->willReturn($coupon);
        }

        $cost = $this->service->calculateByPurchaseDataDTO($productPriceDTO);
        self::assertEquals($cost, $result);
    }

    public function negativePriceTest(): void
    {
        $productPriceDTO = new PurchaseDataDTO();
        $productPriceDTO
            ->setProduct('1')
            ->setCouponCode('code')
            ->setTaxNumber('GR123456789');

        $this->productRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with($productPriceDTO->getProduct())
            ->willReturn(
                (new Product())->setPrice(1000),
            );

        $this->couponRepositoryMock
            ->expects(self::once())
            ->method('findOneByCode')
            ->with($productPriceDTO->getCouponCode())
            ->willReturn(
                (new Coupon())
                    ->setType('fixed')
                    ->setValue(10000),
            );

        $this->expectException(CalculateCostNegativeValueException::class);
        $this->expectExceptionMessage('Negative price');

        $this->service->calculateByPurchaseDataDTO($productPriceDTO);
    }

    public static function calculateByProductPriceDTOProvider(): array
    {
        return [
            'withoutCoupon' => [
                (new Product())->setPrice(10000),
                null,
                'GR123456789',
                12400,
            ],
            'couponPercentage' => [
                (new Product())->setPrice(10000),
                (new Coupon())
                    ->setType('percentage')
                    ->setValue(6),
                'GR123456789',
                11656,
            ],
            'couponFixed' => [
                (new Product())->setPrice(10000),
                (new Coupon())
                    ->setType('fixed')
                    ->setValue(1000),
                'GR123456789',
                11160,
            ],
            'rounding' => [
                (new Product())->setPrice(2000),
                (new Coupon())
                    ->setType('percentage')
                    ->setValue(7),
                'DE123456789',
                2213, // 2213.4
            ],
        ];
    }
}
