<?php
declare(strict_types = 1);

namespace App\Service\Payment\PaymentProcessors;

use App\Exception\PaymentProcessorException;

interface PaymentProcessorInterface
{
    /**
     * @throws PaymentProcessorException
     */
    public function pay(int $price): void;
}
