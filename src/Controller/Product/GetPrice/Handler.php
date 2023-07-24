<?php
declare(strict_types = 1);

namespace App\Controller\Product\GetPrice;

use App\DTO\Product\ProductCostRequest;
use App\DTO\Product\PurchaseDataDTO;
use App\Service\Product\CalculateCostService;
use Psr\Log\LoggerInterface;

readonly class Handler
{
    public function __construct(
        private CalculateCostService $calculateCostService,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(PurchaseDataDTO $productPriceDTO): ProductCostRequest
    {
        try {
            $cost = $this->calculateCostService->calculateByPurchaseDataDTO($productPriceDTO);
        } catch (\Throwable $exception) {
            $this->logger->error(
                $exception->getMessage(),
                [
                    'exception' => $exception,
                    'requestDTO' => $productPriceDTO,
                ],
            );

            throw new \RuntimeException('Не удалось рассчитать стоимость.');
        }

        return new ProductCostRequest($cost / 100);
    }
}
