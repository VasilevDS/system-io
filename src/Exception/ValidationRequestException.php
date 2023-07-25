<?php
declare(strict_types = 1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class ValidationRequestException extends ValidatorException
{
    private ConstraintViolationListInterface $constraintViolationList;

    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        parent::__construct('Error validation request', 400);

        $this->constraintViolationList = $constraintViolationList;
    }

    public function getConstraintViolationList(): ConstraintViolationListInterface
    {
        return $this->constraintViolationList;
    }
}
