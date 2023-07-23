<?php
declare(strict_types=1);

namespace App\Controller\Product;

use App\Controller\ApiController;
use App\DTO\Product\ProductPriceDTO;
use App\Form\Product\ProductPriceForm;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetPriceController extends ApiController
{
    #[Route(path: '/product/price', methods: 'POST')]
    public function getPrice(Request $request): JsonResponse
    {
        $dto = new ProductPriceDTO();
        $form = $this->createForm(ProductPriceForm::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->json([]);
        }

        return $this->formErrorsResponse($form);
    }
}
