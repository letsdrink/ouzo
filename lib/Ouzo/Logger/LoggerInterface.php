<?php
namespace Ouzo\Logger;

interface LoggerInterface
{
    function error($message);

    function info($message);

    function debug($message);

    function warning($message);

    function fatal($message);
}
