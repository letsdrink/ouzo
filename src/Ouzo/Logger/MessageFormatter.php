<?php
namespace Ouzo\Logger;

interface MessageFormatter
{
    function format($logger, $level, $message);
}