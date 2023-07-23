<?php

namespace App\Tests\Unit\Serivce\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\Rules\FranceRules;
use App\Service\TaxNumber\Rules\GermanyRules;
use App\Service\TaxNumber\Rules\ItalyRules;
use App\Service\TaxNumber\TaxCountryHelper;
use PHPUnit\Framework\TestCase;

class FranceRulesTest extends TestCase
{
    /**
     * @throws TaxNumberException
     */
    public function testSuccessful(): void
    {
        $rules = new FranceRules(new TaxCountryHelper());
        $this->expectNotToPerformAssertions();
        $rules->validate('FRqq123456789');
    }

    /**
     * @dataProvider errorProvider
     */
    public function testError(string $taxNumber, string $errorMessage): void
    {
        $rules = new FranceRules(new TaxCountryHelper());
        $this->expectException(TaxNumberException::class);
        $this->expectExceptionMessage($errorMessage);
        $rules->validate($taxNumber);
    }

    public static function errorProvider(): array
    {
        return [
            [
                'IT123456789',
                'Некорректный формат налогового номера.',
            ],
            [
                'FR1234567890',
                'В номере должно быть 13 символов.',
            ],
            [
                'FRqq12345678w',
                'В номере с 4 по 13 символ должно быть только цифры.',
            ],
            [
                'FRqq12345678Q',
                'В номере с 4 по 13 символ должно быть только цифры.',
            ],
        ];
    }
}
