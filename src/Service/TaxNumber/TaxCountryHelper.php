<?php
declare(strict_types=1);

namespace App\Service\TaxNumber;

class TaxCountryHelper
{
    public const TAX_GERMANY = 19;
    public const TAX_ITALY = 22;
    public const TAX_FRANCE = 20;
    public const TAX_GREECE = 24;

    public function getCodeCountryEnum(string $taxNumber): ?CodeCountryEnum
    {
        $codeCountry = mb_substr($taxNumber, 0, 2);

        return CodeCountryEnum::tryFrom($codeCountry);
    }

    public function getTaxPercentage(CodeCountryEnum $codeCountryEnum): int
    {
        return match ($codeCountryEnum) {
            CodeCountryEnum::Germany => self::TAX_GERMANY,
            CodeCountryEnum::Italy => self::TAX_ITALY,
            CodeCountryEnum::France => self::TAX_FRANCE,
            CodeCountryEnum::Greece => self::TAX_GREECE,
        };
    }

    public function getTaxPercentageByTaxNumber(string $taxNumber): ?int
    {
        $codeCountry = $this->getCodeCountryEnum($taxNumber);
        if (null === $codeCountry) {
            return null;
        }

        return $this->getTaxPercentage($codeCountry);
    }
}
