<?php
declare(strict_types=1);

namespace App\Service\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\CodeCountryEnum;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

// FRYYXXXXXXXXX - для жителей Франции
#[AutoconfigureTag(name: 'tax_number.rules')]
#[AsTaggedItem(index: CodeCountryEnum::France->value)]
class FranceRules extends AbstractTaxNumberRules
{
    private const LENGTH = 13;

    /**
     * @throws TaxNumberException
     */
    public function validate(string $taxNumber): void
    {
        $codeCountry = $this->taxCountryHelper->getCodeCountryEnum($taxNumber);
        if (CodeCountryEnum::France !== $codeCountry) {
            throw new TaxNumberException();
        }

        if (self::LENGTH !== strlen($taxNumber)) {
            throw new TaxNumberException(sprintf('В номере должно быть %d символов.', self::LENGTH));
        }

        $onlyLetters = mb_substr($taxNumber, 2, 2);
        if (!ctype_alpha($onlyLetters)) {
            throw new TaxNumberException('В номере с 2 по 4 символ должно быть только буквы.');
        }

        $onlyNumbers = mb_substr($taxNumber, 4);
        if (!ctype_digit($onlyNumbers)) {
            throw new TaxNumberException('В номере с 4 по 13 символ должно быть только цифры.');
        }
    }
}
