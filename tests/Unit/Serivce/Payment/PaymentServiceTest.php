<?php
declare(strict_types = 1);

namespace App\Tests\Unit\Serivce\Payment;

use App\Exception\PaymentProcessorException;
use App\PaymentProcessor\PaypalPaymentProcessor;
use App\PaymentProcessor\StripePaymentProcessor;
use App\Service\Payment\PaymentProcessors\PaypalPaymentProcessorAdapter;
use App\Service\Payment\PaymentProcessors\StripePaymentProcessorAdapter;
use App\Service\Payment\PaymentService;
use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    private MockObject|PaypalPaymentProcessor $paypalPaymentProcessorMock;
    private MockObject|StripePaymentProcessor $stripePaymentProcessorMock;
    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paypalPaymentProcessorMock = $this->getMockBuilder(PaypalPaymentProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stripePaymentProcessorMock = $this->getMockBuilder(StripePaymentProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->service = new PaymentService(
            new ArrayIterator([
                'paypal' => new PaypalPaymentProcessorAdapter($this->paypalPaymentProcessorMock),
                'stripe' => new StripePaymentProcessorAdapter($this->stripePaymentProcessorMock),
            ]),
        );
    }

    public function testPaypalSuccessful(): void
    {
        $this->paypalPaymentProcessorMock
            ->expects(self::once())
            ->method('pay');

        $this->service->pay('paypal', 100);
    }

    public function testStripeSuccessful(): void
    {
        $this->stripePaymentProcessorMock
            ->expects(self::once())
            ->method('processPayment')
            ->with(100)
            ->willReturn(true);

        $this->service->pay('stripe', 100);
    }

    public function testStripeError(): void
    {
        $this->stripePaymentProcessorMock
            ->expects(self::once())
            ->method('processPayment')
            ->with(100)
            ->willReturn(false);

        $this->expectException(PaymentProcessorException::class);
        $this->expectExceptionMessage('Не удалось провести оплату');

        $this->service->pay('stripe', 100);
    }
}
