<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;


interface RequestParameterDeserializer
{
    function arrayToObject(array $params, string $type): object;
}