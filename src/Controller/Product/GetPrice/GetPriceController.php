<?php
declare(strict_types=1);

namespace App\Controller\Product\GetPrice;

use App\Controller\ApiController;
use App\DTO\Product\PurchaseDataDTO;
use App\Form\Product\PurchaseDataForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetPriceController extends ApiController
{
    #[Route(path: '/product/price', methods: 'POST')]
    public function getPrice(Request $request, Handler $handler): JsonResponse
    {
        $dto = new PurchaseDataDTO();
        $form = $this->createForm(PurchaseDataForm::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->json($handler->handle($dto));
        }

        return $this->formErrorsResponse($form);
    }
}
