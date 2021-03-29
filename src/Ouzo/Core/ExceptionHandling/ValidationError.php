<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use JetBrains\PhpStorm\ArrayShape;

class ValidationError extends Error
{
    private ?string $value;

    public function __construct(
        string $message,
        ?string $field = null,
        ?string $value = null,
        ?string $code = null
    )
    {
        parent::__construct($code ?? 0, $message, null, $field);
        $this->value = $value;
    }

    #[ArrayShape(['message' => "string", 'code' => "string", 'field' => "null|string"])]
    public function toArray(): array
    {
        $array = ['message' => $this->getMessage()];
        if ($this->getField()) {
            $array['field'] = $this->getField();
        }
        if ($this->getCode()) {
            $array['code'] = $this->getCode();
        }
        if ($this->value) {
            $array['value'] = $this->value;
        }
        return $array;
    }
}
