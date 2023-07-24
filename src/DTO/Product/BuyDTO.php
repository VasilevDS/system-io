<?php
declare(strict_types = 1);

namespace App\DTO\Product;

class BuyDTO extends PurchaseDataDTO
{
    private string $paymentProcessor;

    public function getPaymentProcessor(): string
    {
        return $this->paymentProcessor;
    }

    public function setPaymentProcessor(string $paymentProcessor): self
    {
        $this->paymentProcessor = $paymentProcessor;

        return $this;
    }
}
