<?php
namespace Ouzo\Tests;

class MockSessionInitializer
{
    public function startSession()
    {
        $_SESSION = isset($_SESSION) ? $_SESSION : array();
    }
}
