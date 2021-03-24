<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Helper;

class ViewUtils
{
    static function fileIncludeTag(?string $type, ?string $url): ?string
    {
        return match ($type) {
            'link' => HtmlElementRenderer::element("link", true)
                    ->setAttribute("rel", "stylesheet")
                    ->setAttribute("type", "text/css")
                    ->setAttribute("href", $url)
                    ->render() . PHP_EOL,

            'script' => HtmlElementRenderer::element("script")
                    ->setAttribute("type", "text/javascript")
                    ->setAttribute("src", $url)
                    ->render() . PHP_EOL,

            default => null
        };
    }
}