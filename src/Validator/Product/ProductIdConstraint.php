<?php
declare(strict_types = 1);

namespace App\Validator\Product;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ProductIdConstraint extends Constraint
{
    public string $message = 'Продукт id:"{{ id }}" не найден.';
}
