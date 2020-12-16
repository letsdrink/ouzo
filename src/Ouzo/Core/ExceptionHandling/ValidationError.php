<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

class ValidationError extends Error
{

    public function __construct(string $message, ?string $field = null)
    {
        parent::__construct(null, $message, null, $field);
    }

    public function toArray()
    {
        $array = ['message' => $this->getMessage()];
        if ($this->getField()) {
            $array['field'] = $this->getField();
        }
        return $array;
    }
}
