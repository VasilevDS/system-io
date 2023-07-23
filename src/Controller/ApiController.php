<?php
declare(strict_types = 1);

namespace App\Controller;

use App\DTO\ErrorResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends AbstractController
{
    protected function apiErrorResponse(
        string $message,
        array $errors = [],
        int $code = Response::HTTP_BAD_REQUEST,
    ): JsonResponse {
        return $this->json(new ErrorResponse($message, $errors), $code);
    }

    protected function formErrorsResponse(FormInterface $form): JsonResponse
    {
        if (!$form->isSubmitted()) {
            return $this->apiErrorResponse('Пустой ввод');
        }

        $errors = [];
        foreach ($form->getErrors(true) as $formError) {
            $path = $formError->getOrigin()?->getName();
            $form = $formError->getOrigin();
            if (!$path && $form->isRoot()) {
                $errors[] = [
                    'propertyName' => null,
                    'message' => $formError->getMessage(),
                ];
            } else {
                while ($form->getParent() && !$form->getParent()->isRoot()) {
                    $form = $form->getParent();
                    $path = sprintf('%s.%s', $form->getName(), $path);
                }
                $errors[] = [
                    'propertyName' => $path,
                    'message' => $formError->getMessage(),
                ];
            }
        }

        return $this->apiErrorResponse('Ошибка валидации запроса', $errors);
    }
}
