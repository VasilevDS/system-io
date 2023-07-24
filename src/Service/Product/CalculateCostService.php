<?php
declare(strict_types = 1);

namespace App\Service\Product;

use App\DTO\Product\PurchaseDataDTO;
use App\Entity\Coupon;
use App\Entity\Product;
use App\Exception\CalculateCostNegativeValueException;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\TaxNumber\TaxCountryHelper;

readonly class CalculateCostService
{
    public function __construct(
        private ProductRepository $productRepository,
        private CouponRepository $couponRepository,
        private TaxCountryHelper $taxCountryHelper,
    ) {
    }

    /**
     * @throws CalculateCostNegativeValueException
     */
    public function calculateByPurchaseDataDTO(PurchaseDataDTO $productPriceDTO): int
    {
        /** @var Product $product */
        $product = $this->productRepository->find($productPriceDTO->getProduct());
        $productPrice = $product->getPrice();

        $coupon = null;
        if (null !== $productPriceDTO->getCouponCode()) {
            $coupon = $this->couponRepository->findOneByCode($productPriceDTO->getCouponCode());
        }

        $tax = $this->taxCountryHelper->getTaxPercentageByTaxNumber($productPriceDTO->getTaxNumber());

        $cost = $this->calculate($productPrice, $tax, $coupon);
        if ($cost < 0) {
            throw new CalculateCostNegativeValueException('Negative price');
        }

        return $cost;
    }

    private function calculate(int $productPrice, int $taxPercentage, ?Coupon $coupon): int
    {
        $cost = $productPrice;
        if (null !== $coupon) {
            if (Coupon::TYPE_FIXED === $coupon->getType()) {
                $cost -= $coupon->getValue();
            } else {
                $cost = (int) round($cost * (100 - $coupon->getValue()) / 100);
            }
        }

        return (int) round($cost * (100 + $taxPercentage) / 100);
    }
}
