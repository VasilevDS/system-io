<?php
declare(strict_types = 1);

namespace App\Service\Payment;

use App\Exception\PaymentProcessorException;
use App\Service\Payment\PaymentProcessors\PaymentProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Traversable;

class PaymentService
{
    public const PAYMENT_PROCESSOR_PAYPAL = 'paypal';
    public const PAYMENT_PROCESSOR_STRIPE = 'stripe';

    /**
     * @var PaymentProcessorInterface[]
     */
    private array $paymentProcessors;

    public function __construct(
        #[TaggedIterator(tag: 'payment_processor', indexAttribute: 'key')]
        Traversable $paymentProcessors,
    )
    {
        $this->paymentProcessors = iterator_to_array($paymentProcessors);
    }

    /**
     * @throws PaymentProcessorException
     */
    public function pay(string $processorType, int $price): void
    {
        if (!isset($this->paymentProcessors[$processorType])) {
            throw new PaymentProcessorException('Не удалось определить платежную систему.');
        }

        $paymentProcessor = $this->paymentProcessors[$processorType];

        $paymentProcessor->pay($price);
    }
}
