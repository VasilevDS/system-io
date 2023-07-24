<?php
declare(strict_types = 1);

namespace App\DTO\Product;

use App\DTO\SuccessfulResponse;

class ProductCostRequest extends SuccessfulResponse
{
    public function __construct(public readonly float $cost)
    {
        parent::__construct();
    }
}
