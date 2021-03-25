<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

interface ViewRenderer
{
    public function render(): string;

    public function getViewPath(): string;
}
