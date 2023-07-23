<?php
declare(strict_types = 1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['convertJsonStringToArray', 10],
            ],
        ];
    }

    public function convertJsonStringToArray(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ('json' !== $request->getContentTypeFormat() || !$request->getContent() || '' === $request->getContent()) {
            return;
        }

        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new BadRequestHttpException(sprintf('invalid json body: %s', $exception->getMessage()));
        }

        $request->request->replace(\is_array($data) ? $data : []);
    }
}
