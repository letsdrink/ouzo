<?php
namespace Ouzo\Logger;

use Ouzo\FrontController;

class DefaultMessageFormatter implements MessageFormatter
{
    public function format($logger, $level, $message)
    {
        return sprintf("%s %s: [ID: %s] %s", $logger, $level, FrontController::$requestId, $message);
    }
}