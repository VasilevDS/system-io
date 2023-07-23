<?php

namespace App\Exception;

class TaxNumberException extends \Exception
{
    public function __construct(string $message = 'Некорректный формат налогового номера.')
    {
        parent::__construct($message, 400);
    }
}
