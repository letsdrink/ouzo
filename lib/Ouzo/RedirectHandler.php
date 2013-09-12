<?php
namespace Ouzo;

class RedirectHandler
{
    public function redirect($url)
    {
        header('Location: ' . $url);
    }
}