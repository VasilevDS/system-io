<?php
declare(strict_types = 1);

namespace App\EventSubscriber;

use App\DTO\ErrorResponse;
use App\Exception\ValidationRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class ValidationExceptionSubscribe implements EventSubscriberInterface
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'getResponseValidation',
        ];
    }

    public function getResponseValidation(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof ValidationRequestException) {
            $errors = [];
            foreach ($exception->getConstraintViolationList() as $violation) {
                $message = $violation->getParameters()['hint'] ?? $violation->getMessage();
                $errors[] = [
                    'propertyName' => $violation->getPropertyPath(),
                    'message' => $message,
                ];
            }

            $responseData = $this->serializer->serialize(new ErrorResponse($exception->getMessage(), $errors), 'json');

            $event->setResponse(new JsonResponse($responseData, $exception->getCode(), [], true));
        }
    }
}
