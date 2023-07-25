<?php
declare(strict_types = 1);

namespace App\Controller\Product\Buy;

use App\Controller\ApiController;
use App\DTO\Product\BuyDTO;
use App\Exception\PaymentProcessorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BuyController extends ApiController
{
    #[Route(path: '/product/buy', methods: 'POST')]
    public function buy(BuyDTO $buyDTO, Handler $handler): JsonResponse
    {
        try {
            return $this->json($handler->handle($buyDTO));
        } catch (PaymentProcessorException|\RuntimeException $exception) {
            return $this->apiErrorResponse($exception->getMessage());
        }
    }
}
