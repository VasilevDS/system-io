<?php
declare(strict_types=1);

namespace App\Service\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\CodeCountryEnum;

class GreeceRules extends AbstractTaxNumberRules
{
    private const LENGTH = 11;

    /**
     * @throws TaxNumberException
     */
    public function validate(string $taxNumber): void
    {
        $codeCountry = $this->taxCountryHelper->getCodeCountryEnum($taxNumber);
        if (CodeCountryEnum::Greece !== $codeCountry) {
            throw new TaxNumberException();
        }

        if (self::LENGTH !== strlen($taxNumber)) {
            throw new TaxNumberException(sprintf('В номере должно быть %d символов.', self::LENGTH));
        }

        $onlyNumbers = mb_substr($taxNumber, 2);
        if (!ctype_digit($onlyNumbers)) {
            throw new TaxNumberException('В номере с 2 по 10 символ должно быть только цифры.');
        }
    }
}
