<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class OuzoException extends Exception
{
    private int $httpCode;
    private array $errors;
    private array $headers = [];

    /**
     * @param int $httpCode
     * @param string $message
     * @param Error[]|Error $errors
     * @param string[] $headers
     */
    public function __construct(
        int $httpCode,
        string $message,
        array $errors,
        array $headers = []
    )
    {
        $this->httpCode = $httpCode;
        $this->errors = Arrays::toArray($errors);
        $this->headers = $headers;
        $message .= " " . implode(", ", $this->getErrorMessages());
        parent::__construct($message);
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /** @return string[] */
    public function getErrorMessages(): array
    {
        return Arrays::map($this->errors, Functions::extractField('message'));
    }

    public function asExceptionData(): OuzoExceptionData
    {
        return new OuzoExceptionData($this->httpCode, $this->errors, StackTrace::forException($this), $this->getHeaders(), get_class($this));
    }

    /** @return string[] */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
