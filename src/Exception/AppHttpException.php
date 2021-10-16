<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class AppHttpException extends HttpException
{
    private array $errors;

    public function __construct(int $statusCode, ?string $message = 'A validation error occurred...', $errors = [], \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}