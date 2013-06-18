<?php
namespace Thulium\Uri;

class PathProvider
{
    public function getPath()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    }
}