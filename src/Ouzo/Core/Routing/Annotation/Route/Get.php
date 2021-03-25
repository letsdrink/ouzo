<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Annotation\Route;

use Attribute;
use Ouzo\Http\HttpMethod;
use Ouzo\Routing\Annotation\Route;

#[Attribute(Attribute::TARGET_METHOD)]
class Get extends Route
{
    public function __construct(string $path, ?int $httpResponseCode = null)
    {
        parent::__construct($path, [HttpMethod::GET], $httpResponseCode);
    }
}
