<?php
namespace Thulium\Tests;

class MockSessionInitializer
{
    public function startSession()
    {
        $_SESSION = isset($_SESSION) ? $_SESSION : array();
    }
}