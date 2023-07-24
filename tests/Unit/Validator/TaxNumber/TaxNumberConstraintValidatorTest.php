<?php
declare(strict_types = 1);

namespace App\Tests\Unit\Validator\TaxNumber;

use App\Service\TaxNumber\Rules\FranceRules;
use App\Service\TaxNumber\Rules\GermanyRules;
use App\Service\TaxNumber\Rules\GreeceRules;
use App\Service\TaxNumber\Rules\ItalyRules;
use App\Service\TaxNumber\TaxCountryHelper;
use App\Validator\TaxNumber\TaxNumberConstraint;
use App\Validator\TaxNumber\TaxNumberConstraintValidator;
use ArrayIterator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class TaxNumberConstraintValidatorTest extends TestCase
{
    private ExecutionContextInterface|MockObject $executionContextMock;
    private ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock;
    private TaxNumberConstraintValidator $validator;
    private TaxNumberConstraint $constraint;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executionContextMock = $this->getMockBuilder(ExecutionContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->constraintViolationBuilderMock = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new TaxCountryHelper();
        $this->validator = new TaxNumberConstraintValidator(
            new ArrayIterator([
                'DE' => new GermanyRules($helper),
                'GR' => new GreeceRules($helper),
                'FR' => new FranceRules($helper),
                'IT' => new ItalyRules($helper),
            ]),
            $helper,
        );
        $this->constraint = new TaxNumberConstraint();
    }

    /**
     * @dataProvider successfulProvider
     */
    public function testSuccessful(string $taxNumber): void
    {
        $this->executionContextMock
            ->expects(self::never())
            ->method('buildViolation');

        $this->constraintViolationBuilderMock
            ->expects(self::never())
            ->method('addViolation');

        $this->validator->initialize($this->executionContextMock);
        $this->validator->validate($taxNumber, $this->constraint);
    }

    /**
     * @dataProvider errorProvider
     */
    public function testError(string $taxNumber, string $message): void
    {
        $this->executionContextMock
            ->expects(self::once())
            ->method('buildViolation')
            ->with($message)
            ->willReturn($this->constraintViolationBuilderMock);

        $this->constraintViolationBuilderMock
            ->expects(self::once())
            ->method('addViolation');

        $this->validator->initialize($this->executionContextMock);
        $this->validator->validate($taxNumber, $this->constraint);
    }

    public static function successfulProvider(): array
    {
        return [
            ['DE123456789'],
            ['GR123456789'],
            ['IT12345678900'],
            ['FRxx123456789'],
        ];
    }

    public static function errorProvider(): array
    {
        return [
            'invalidCodeCountry' => [
                'XX123456789',
                'Не удалось определить код страны',
            ],
            'lengthError' => [
                'DE1234567890',
                'В номере должно быть 11 символов.',
            ],
        ];
    }
}
