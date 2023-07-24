<?php
declare(strict_types = 1);

namespace App\Tests\Unit\Validator\Product;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Validator\Product\ProductIdConstraint;
use App\Validator\Product\ProductIdConstraintValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ProductIdConstraintValidatorTest extends TestCase
{
    private ExecutionContextInterface|MockObject $executionContextMock;
    private ConstraintViolationBuilderInterface|MockObject $constraintViolationBuilderMock;
    private MockObject|ProductRepository $productRepositoryMock;
    private ProductIdConstraint $constraint;
    private ProductIdConstraintValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executionContextMock = $this->getMockBuilder(ExecutionContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->constraintViolationBuilderMock = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->constraint = new ProductIdConstraint();
        $this->productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->validator = new ProductIdConstraintValidator($this->productRepositoryMock);
    }

    public function testSuccessful(): void
    {
        $productId = '1';
        $this->productRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with($productId)
            ->willReturn(new Product());

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
        $this->validator->validate($productId, $this->constraint);
    }

    public function testError(): void
    {
        $productId = '1';
        $this->productRepositoryMock
            ->expects(self::once())
            ->method('find')
            ->with($productId)
            ->willReturn(null);

        $this->executionContextMock
            ->expects(self::once())
            ->method('buildViolation')
            ->with('Продукт id:"{{ id }}" не найден.')
            ->willReturn($this->constraintViolationBuilderMock);

        $this->constraintViolationBuilderMock
            ->expects(self::once())
            ->method('setParameter')
            ->with('{{ id }}', '1')
            ->willReturn($this->constraintViolationBuilderMock);

        $this->constraintViolationBuilderMock
            ->expects(self::once())
            ->method('addViolation');

        $this->validator->initialize($this->executionContextMock);
        $this->validator->validate($productId, $this->constraint);
    }
}
