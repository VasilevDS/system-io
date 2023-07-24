<?php
declare(strict_types=1);

namespace App\Service\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\TaxCountryHelper;

abstract class AbstractTaxNumberRules
{
    public function __construct(
        protected readonly TaxCountryHelper $taxCountryHelper,
    ) {
    }

    /**
     * @throws TaxNumberException
     */
    abstract public function validate(string $taxNumber): void;
}
