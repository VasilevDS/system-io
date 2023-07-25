<?php
declare(strict_types = 1);

namespace App\Resolver;

use App\Exception\ValidationRequestException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AutoconfigureTag(name: 'controller.argument_value_resolver')]
class RequestDTOResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!is_subclass_of($argument->getType(), RequestDTOResolverInterface::class)) {
            return [];
        }

        $content = $request->getContent();
        if ('json' !== $request->getContentTypeFormat()) {
            return [];
        }

        try {
            $dto = $this->serializer->deserialize(
                $content,
                $argument->getType(),
                'json',
                [DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true]
            );
        } catch (PartialDenormalizationException $exception) {
            $violations = new ConstraintViolationList();
            /** @var NotNormalizableValueException $valueException */
            foreach ($exception->getErrors() as $valueException) {
                $message = sprintf(
                    'The type must be one of "%s" ("%s" given).',
                    implode(', ', $valueException->getExpectedTypes()),
                    $valueException->getCurrentType(),
                );
                $parameters = [];
                if ($valueException->canUseMessageForUser()) {
                    $parameters['hint'] = $valueException->getMessage();
                }
                $violations->add(new ConstraintViolation($message, '', $parameters, null, $valueException->getPath(), null));
            }

            throw new ValidationRequestException($violations);
        }

        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            throw new ValidationRequestException($errors);
        }

        yield $dto;
    }
}
