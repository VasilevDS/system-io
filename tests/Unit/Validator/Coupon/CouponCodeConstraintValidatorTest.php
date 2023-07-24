<?php
declare(strict_types = 1);

namespace App\Tests\Unit\Validator\Coupon;

use App\Entity\Coupon;
use App\Repository\CouponRepository;
use App\Validator\Coupon\CouponCodeConstraint;
use App\Validator\Coupon\CouponCodeConstraintValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CouponCodeConstraintValidatorTest extends TestCase
{
    private ExecutionContextInterface|MockObject $executionContextMock;
    private ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock;
    private CouponRepository|MockObject $couponRepositoryMock;
    private CouponCodeConstraint $constraint;
    private CouponCodeConstraintValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executionContextMock = $this->getMockBuilder(ExecutionContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->constraintViolationBuilderMock = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->constraint = new CouponCodeConstraint();
        $this->couponRepositoryMock = $this->getMockBuilder(CouponRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator = new CouponCodeConstraintValidator($this->couponRepositoryMock);
    }

    public function testSuccessful(): void
    {
        $couponCode = 'D15';
        $this->couponRepositoryMock
            ->expects(self::once())
            ->method('findOneByCode')
            ->with($couponCode)
            ->willReturn(new Coupon());

        $this->executionContextMock
            ->expects(self::never())
            ->method('buildViolation');

        $this->constraintViolationBuilderMock
            ->expects(self::never())
            ->method('setParameter');

        $this->constraintViolationBuilderMock
            ->expects(self::never())
            ->method('addViolation');

        $this->validator->initialize($this->executionContextMock);
        $this->validator->validate($couponCode, $this->constraint);
    }

    public function testError(): void
    {
        $couponCode = 'D15';
        $this->couponRepositoryMock
            ->expects(self::once())
            ->method('findOneByCode')
            ->with($couponCode)
            ->willReturn(null);

        $this->executionContextMock
            ->expects(self::once())
            ->method('buildViolation')
            ->with('Купон по коду:{{ code }} не найден.')
            ->willReturn($this->constraintViolationBuilderMock);

        $this->constraintViolationBuilderMock
            ->expects(self::once())
            ->method('setParameter')
            ->with('{{ code }}', 'D15')
            ->willReturn($this->constraintViolationBuilderMock);

        $this->constraintViolationBuilderMock
            ->expects(self::once())
            ->method('addViolation');

        $this->validator->initialize($this->executionContextMock);
        $this->validator->validate($couponCode, $this->constraint);
    }
}
