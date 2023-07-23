<?php

namespace App\Tests\Unit\Serivce\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\Rules\GermanyRules;
use App\Service\TaxNumber\Rules\GreeceRules;
use App\Service\TaxNumber\TaxCountryHelper;
use PHPUnit\Framework\TestCase;

class GreeceRulesTest extends TestCase
{
    /**
     * @throws TaxNumberException
     */
    public function testSuccessful(): void
    {
        $rules = new GreeceRules(new TaxCountryHelper());
        $this->expectNotToPerformAssertions();
        $rules->validate('GR123456789');
    }

    /**
     * @dataProvider errorProvider
     */
    public function testError(string $taxNumber, string $errorMessage): void
    {
        $rules = new GreeceRules(new TaxCountryHelper());
        $this->expectException(TaxNumberException::class);
        $this->expectExceptionMessage($errorMessage);
        $rules->validate($taxNumber);
    }

    public static function errorProvider(): array
    {
        return [
            [
                'DE123456789',
                'Некорректный формат налогового номера.',
            ],
            [
                'GR12345678',
                'В номере должно быть 11 символов.',
            ],
            [
                'GR12345678Q',
                'В номере с 2 по 10 символ должно быть только цифры.',
            ],
        ];
    }
}
