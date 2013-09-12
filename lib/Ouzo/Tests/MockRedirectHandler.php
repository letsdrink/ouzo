<?php
namespace Ouzo\Tests;

class MockRedirectHandler
{
    private $_location;

    public function redirect($url)
    {
        $this->_location = $url;

        return $this;
    }

    public function getLocation()
    {
        return $this->_location;
    }
}