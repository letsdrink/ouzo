<?php
namespace Ouzo\Tests;

class MockShell
{
    public function out($message)
    {
        echo "$message\n";
    }
}