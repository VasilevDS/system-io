<?php
declare(strict_types = 1);

namespace App\Validator\Product;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ProductIdConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProductIdConstraint) {
            throw new UnexpectedTypeException($constraint, ProductIdConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $product = $this->productRepository->find($value);
        if (null === $product) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ id }}', $value)
                ->addViolation();
        }
    }
}
