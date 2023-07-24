<?php
declare(strict_types = 1);

namespace App\Service\Payment\PaymentProcessors;

use App\Exception\PaymentProcessorException;
use App\PaymentProcessor\PaypalPaymentProcessor;
use App\Service\Payment\PaymentService;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'payment_processor')]
#[AsTaggedItem(index: PaymentService::PAYMENT_PROCESSOR_PAYPAL)]
readonly class PaypalPaymentProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(private PaypalPaymentProcessor $paypalPaymentProcessor)
    {
    }

    public function pay(int $price): void
    {
        try {
            $this->paypalPaymentProcessor->pay($price);
        } catch (\Throwable $exception) {
            throw new PaymentProcessorException(sprintf('Не удалось провести оплату. %s', $exception->getMessage()));
        }
    }
}
