<?php
declare(strict_types = 1);

namespace App\Validator\Coupon;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CouponCodeConstraint extends Constraint
{
    public string $message = 'Купон по коду:{{ code }} не найден.';
}
