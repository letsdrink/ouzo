<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class HeaderSender
{
    public function send($headers)
    {
        Arrays::map($headers, function ($header) {
            header($header);
        });
    }
}
