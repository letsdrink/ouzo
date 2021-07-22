<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Ouzo\Routing\RouteRule;

interface ViewNameProvider
{
    function getViewName(RouteRule $rule, ?string $action): string;
}