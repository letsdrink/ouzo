<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

class ValidationError extends Error
{
    private ?string $value;

    public function __construct(string $message, ?string $field = null, ?string $value = null, ?string $code = null)
    {
        parent::__construct(null, $message, null, $field);
        $this->value = $value;
        $this->code = $code;
    }

    public function toArray()
    {
        $array = ['message' => $this->getMessage()];
        if ($this->getField()) {
            $array['field'] = $this->getField();
        }
        if ($this->getCode()) {
            $array['code'] = $this->code;
        }
        if ($this->value) {
            $array['value'] = $this->value;
        }
        return $array;
    }
}
