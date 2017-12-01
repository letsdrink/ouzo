<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class Notice
{
    /** @var null|string */
    private $url;
    /** @var string */
    private $message;

    /**
     * @param string $message
     * @param null|string $url
     */
    public function __construct($message, $url = null)
    {
        $this->url = $url ? $url : null;
        $this->message = $message;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function requestUrlMatches()
    {
        return $this->getUrl() == null || !strcmp(Uri::removePrefix($this->getCurrentPath()), Uri::removePrefix($this->getUrl()));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    private function getCurrentPath()
    {
        $uri = new Uri();
        $fullUrlWithPrefix = $uri->getFullUrlWithPrefix();
        $parts = parse_url($fullUrlWithPrefix);
        return Arrays::getValue($parts, 'path', $fullUrlWithPrefix);
    }
}
