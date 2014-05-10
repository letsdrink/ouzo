<?php
namespace Ouzo;

class Notice
{
    private $url;
    private $message;

    public function __construct($message, $url = null)
    {
        $this->url = $url ? $url : null;
        $this->message = $message;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function requestUrlMatches()
    {
        return $this->getUrl() == null || !strcmp($this->getCurrentPath(), $this->getUrl());
    }

    public function __toString()
    {
        return $this->message;
    }

    private function getCurrentPath()
    {
        $uri = new Uri();
        return $uri->getPath();
    }
}