<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tests;

class MockHeaderSender
{
    private $_headers;

    public function send($headers)
    {
        $this->_headers = $headers;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }
}
