<?php

namespace App\Tests\Unit\Serivce\TaxNumber\Rules;

use App\Exception\TaxNumberException;
use App\Service\TaxNumber\Rules\GermanyRules;
use App\Service\TaxNumber\Rules\ItalyRules;
use App\Service\TaxNumber\TaxCountryHelper;
use PHPUnit\Framework\TestCase;

class ItalyRulesTest extends TestCase
{
    /**
     * @throws TaxNumberException
     */
    public function testSuccessful(): void
    {
        $rules = new ItalyRules(new TaxCountryHelper());
        $this->expectNotToPerformAssertions();
        $rules->validate('IT12345678911');
    }

    /**
     * @dataProvider errorProvider
     */
    public function testError(string $taxNumber, string $errorMessage): void
    {
        $rules = new ItalyRules(new TaxCountryHelper());
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
                'IT12345678',
                'В номере должно быть 13 символов.',
            ],
            [
                'IT1234567891Q',
                'В номере с 2 по 13 символ должно быть только цифры.',
            ],
        ];
    }
}
