<?php
declare(strict_types = 1);

namespace App\Validator\Coupon;

use App\Repository\CouponRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CouponCodeConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CouponRepository $couponRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CouponCodeConstraint) {
            throw new UnexpectedTypeException($constraint, CouponCodeConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $coupon = $this->couponRepository->findOneByCode($value);
        if (null === $coupon) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation();
        }
    }
}
