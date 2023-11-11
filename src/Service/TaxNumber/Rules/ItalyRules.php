<?php
declare(strict_types=1);

namespace App\Service\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\CodeCountryEnum;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

// ITXXXXXXXXXXX - для жителей Италии
#[AutoconfigureTag(name: 'tax_number.rules')]
#[AsTaggedItem(index: CodeCountryEnum::Italy->value)]
class ItalyRules extends AbstractTaxNumberRules
{
    private const LENGTH = 13;

    /**
     * @throws TaxNumberException
     */
    public function validate(string $taxNumber): void
    {
        $codeCountry = $this->taxCountryHelper->getCodeCountryEnum($taxNumber);
        if (CodeCountryEnum::Italy !== $codeCountry) {
            throw new TaxNumberException();
        }

        if (self::LENGTH !== strlen($taxNumber)) {
            throw new TaxNumberException(sprintf('В номере должно быть %d символов.', self::LENGTH));
        }

        $onlyNumbers = mb_substr($taxNumber, 2);
        if (!ctype_digit($onlyNumbers)) {
            throw new TaxNumberException('В номере с 2 по 13 символ должно быть только цифры.');
        }
    }

    public function testCommitFirst(): string
    {
        return 'first';
    }

    public function testCommitSecond(): string
    {
        return 'second';
    }

    public function testCommitThird(): string
    {
        return 'third';
    }
}
