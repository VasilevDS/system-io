<?php

namespace App\DTO\Product;

use App\Resolver\RequestDTOResolverInterface;
use App\Validator\Coupon\CouponCodeConstraint;
use App\Validator\Product\ProductIdConstraint;
use App\Validator\TaxNumber\TaxNumberConstraint;

readonly class PurchaseDataDTO implements RequestDTOResolverInterface
{
    public function __construct(
        #[ProductIdConstraint()]
        public string $product,
        #[TaxNumberConstraint()]
        public string $taxNumber,
        #[CouponCodeConstraint()]
        public ?string $couponCode = null,
    ) {
    }
}
