<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;


use Exception;

class RequestParameterSerializer
{
    public function arrayToObject(array $params, string $type): object
    {
        throw new Exception('Provide request parameter serializer implementation if you want to use this feature');
    }

    public function objectToJson($data): string
    {
        throw new Exception('Provide request parameter serializer implementation if you want to use this feature');
    }
}