<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

abstract class Widget
{
    /**
     * @var View
     */
    protected $_view;

    abstract public function render();
}
