<?php
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
