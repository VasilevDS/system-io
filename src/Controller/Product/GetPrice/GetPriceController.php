<?php
declare(strict_types=1);

namespace App\Controller\Product\GetPrice;

use App\Controller\ApiController;
use App\DTO\Product\PurchaseDataDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GetPriceController extends ApiController
{
    #[Route(path: '/product/price', methods: 'POST')]
    public function getPrice(PurchaseDataDTO $purchaseDataDTO, Handler $handler): JsonResponse
    {
        try {
            return $this->json($handler->handle($purchaseDataDTO));
        } catch (\Throwable $exception) {
            return $this->apiErrorResponse($exception->getMessage());
        }
    }
}
