<?php
namespace Thulium;

class RedirectHandler
{
    public function redirect($url)
    {
        header('Location: ' . $url);
    }
}