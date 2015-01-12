<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Logger;

interface LoggerInterface
{
    public function setName($name);

    public function error($message, $params = null);

    public function info($message, $params = null);

    public function debug($message, $params = null);

    public function warning($message, $params = null);

    public function fatal($message, $params = null);
}
