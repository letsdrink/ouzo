<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;


use Exception;

class RequestParameterDeserializer
{
    public function arrayToObject(array $params, string $type): object
    {
        throw new Exception('Provide request parameter deserializer implementation if you want to use this feature');
    }
}