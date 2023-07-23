<?php
declare(strict_types=1);

namespace App\Service\TaxNumber;

enum CodeCountryEnum: string
{
    case Germany = 'DE';
    case Italy = 'IT';
    case Greece = 'GR';
    case France = 'FR';
}
