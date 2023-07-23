<?php

namespace App\Validator;

use App\Service\TaxNumber\CodeCountryEnum;
use App\Service\TaxNumber\TaxCountryHelper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Traversable;

class TaxNumberValidator extends ConstraintValidator
{
    private array $taxNumbersRules;

    public function __construct(
        #[TaggedIterator(tag: 'tax_number.rules', indexAttribute: 'key')]
        Traversable $taxNumbersRules,
        private readonly TaxCountryHelper $taxCountryHelper,
    ) {
        $this->taxNumbersRules = iterator_to_array($taxNumbersRules);
    }

    /**
     * DEXXXXXXXXX - для жителей Германии

    ITXXXXXXXXXXX - для жителей Италии

    GRXXXXXXXXX - для жителей Греции,

    FRYYXXXXXXXXX - для жителей Франции

    где:

    первые два символа - это код страны
    X - любая цифра от 0 до 9,
    Y - любая буква
    Обратите внимание, что длина налогового номера разная для разных стран.
     * Форматы налоговых номеров могут меняться, что случается редко (Это зависит от законодательства)
     */


    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $codeCountry = $this->taxCountryHelper->getCodeCountryEnum($value);
        $rules = $this->taxNumbersRules[$codeCountry];

        dump($rules);
    }

    private function getCodeCountry(string $taxNumber): ?CodeCountryEnum
    {
        $codeCountry = mb_substr($taxNumber, 0, 2);

        return CodeCountryEnum::tryFrom($codeCountry);
    }
}
