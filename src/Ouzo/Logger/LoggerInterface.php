<?php
namespace Ouzo\Logger;

interface LoggerInterface
{
    public function setName($name);

    public function error($message, $params = null);

    public function info($message, $params = null);

    public function debug($message, $params = null);

    public function warning($message, $params = null);

    public function fatal($message, $params = null);
}