<?php
declare(strict_types = 1);

namespace App\Controller\Product\Buy;

use App\Controller\ApiController;
use App\DTO\Product\BuyDTO;
use App\Exception\PaymentProcessorException;
use App\Form\Product\BuyForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BuyController extends ApiController
{
    #[Route(path: '/product/buy', methods: 'POST')]
    public function buy(Request $request, Handler $handler): JsonResponse
    {
        $dto = new BuyDTO();
        $form = $this->createForm(BuyForm::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $response = $handler->handle($dto);
            } catch (PaymentProcessorException|\RuntimeException $exception) {
                return $this->apiErrorResponse($exception->getMessage());
            }

            return $this->json($response);
        }

        return $this->formErrorsResponse($form);
    }
}
