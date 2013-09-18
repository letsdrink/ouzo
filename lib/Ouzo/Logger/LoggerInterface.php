<?php
namespace Ouzo\Logger;

interface LoggerInterface
{
    function setName($name);

    function error($message, $params = null);

    function info($message, $params = null);

    function debug($message, $params = null);

    function warning($message, $params = null);

    function fatal($message, $params = null);
}
