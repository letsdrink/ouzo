<?php
namespace Ouzo\Logger;

interface MessageFormatter
{
    public function format($logger, $level, $message);
}