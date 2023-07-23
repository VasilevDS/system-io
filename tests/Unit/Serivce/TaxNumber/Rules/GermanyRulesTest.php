<?php

namespace App\Tests\Unit\Serivce\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\Rules\GermanyRules;
use App\Service\TaxNumber\TaxCountryHelper;
use PHPUnit\Framework\TestCase;

class GermanyRulesTest extends TestCase
{
    /**
     * @throws TaxNumberException
     */
    public function testSuccessful(): void
    {
        $rules = new GermanyRules(new TaxCountryHelper());
        $this->expectNotToPerformAssertions();
        $rules->validate('DE123456789');
    }

    /**
     * @dataProvider errorProvider
     */
    public function testError(string $taxNumber, string $errorMessage): void
    {
        $rules = new GermanyRules(new TaxCountryHelper());
        $this->expectException(TaxNumberException::class);
        $this->expectExceptionMessage($errorMessage);
        $rules->validate($taxNumber);
    }

    public static function errorProvider(): array
    {
        return [
            [
                'GE123456789',
                'Некорректный формат налогового номера.',
            ],
            [
                'DE12345678',
                'В номере должно быть 11 символов.',
            ],
            [
                'DE12345678Q',
                'В номере с 2 по 10 символ должно быть только цифры.',
            ],
        ];
    }
}
