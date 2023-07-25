<?php
declare(strict_types = 1);

namespace App\DTO\Product;

use App\Service\Payment\PaymentService;
use Symfony\Component\Validator\Constraints as Assert;

readonly class BuyDTO extends PurchaseDataDTO
{
    public function __construct(
        #[Assert\Choice([PaymentService::PAYMENT_PROCESSOR_PAYPAL, PaymentService::PAYMENT_PROCESSOR_STRIPE])]
        public string $paymentProcessor,
        string $product,
        string $taxNumber,
        ?string $couponCode = null,
    ) {
        parent::__construct($product, $taxNumber, $couponCode);
    }
}
