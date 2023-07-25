<?php
declare(strict_types = 1);

namespace App\Controller\Product\Buy;

use App\DTO\Product\BuyDTO;
use App\DTO\SuccessfulResponse;
use App\Exception\PaymentProcessorException;
use App\Service\Payment\PaymentService;
use App\Service\Product\CalculateCostService;
use Psr\Log\LoggerInterface;

readonly class Handler
{
    public function __construct(
        private CalculateCostService $calculateCostService,
        private PaymentService $paymentService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws PaymentProcessorException|\RuntimeException
     */
    public function handle(BuyDTO $buyDTO): SuccessfulResponse
    {
        try {
            $price = $this->calculateCostService->calculateByPurchaseDataDTO($buyDTO);
            $this->paymentService->pay($buyDTO->paymentProcessor, $price);
        } catch (PaymentProcessorException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->logger->error(
                $exception->getMessage(),
                [
                    'exception' => $exception,
                    'requestDTO' => $buyDTO,
                ],
            );

            throw new \RuntimeException('Не удалось выполнить покупку.');
        }

        return new SuccessfulResponse();
    }
}
