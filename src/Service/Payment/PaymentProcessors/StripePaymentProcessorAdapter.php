<?php
declare(strict_types = 1);

namespace App\Service\Payment\PaymentProcessors;

use App\Exception\PaymentProcessorException;
use App\PaymentProcessor\StripePaymentProcessor;
use App\Service\Payment\PaymentService;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: 'payment_processor')]
#[AsTaggedItem(index: PaymentService::PAYMENT_PROCESSOR_STRIPE)]
readonly class StripePaymentProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(private StripePaymentProcessor $stripePaymentProcessor)
    {
    }

    public function pay(int $price): void
    {
        try {
            $result = $this->stripePaymentProcessor->processPayment($price);
        } catch (\Throwable $exception) {
            throw new PaymentProcessorException(sprintf('Не удалось провести оплату. %s', $exception->getMessage()));
        }

        if (!$result) {
            throw new PaymentProcessorException('Не удалось провести оплату');
        }
    }
}
