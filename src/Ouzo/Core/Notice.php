<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;

class Notice
{
    public function __construct(private string $message, private ?string $url = null)
    {
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function requestUrlMatches(Uri $uri): bool
    {
        return $this->getUrl() == null || strcmp(
                Uri::removePrefix($this->getCurrentPath($uri)),
                Uri::removePrefix($this->getUrlWithoutQuery($this->getUrl()))
            ) === 0;
    }

    public function __toString(): string
    {
        return $this->message;
    }

    private function getCurrentPath(Uri $uri): string
    {
        $url = $uri->getFullUrlWithPrefix();
        return $this->getUrlWithoutQuery($url);
    }

    private function getUrlWithoutQuery(string $url): string
    {
        $parts = parse_url($url);
        return Arrays::getValue($parts, 'path', $url);
    }
}
