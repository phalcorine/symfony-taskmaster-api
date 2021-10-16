<?php

namespace App\Validation;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ApiRequestValidationErrorModel
{
    private $errors = [];

    private string $message;

    public function __construct(ConstraintViolationListInterface $violationList, string $message = "A validation error occurred...")
    {
        if ($violationList != null) {
            $errors = [];

            /** @var ConstraintViolation $violation */
            foreach ($violationList as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }

            $this->errors = $errors;
        }
        $this->message = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}