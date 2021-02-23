<?php

namespace Ouzo\ExceptionHandling;

use ErrorException;
use Ouzo\Utilities\Objects;

class CliErrorRenderer
{
    public function render(OuzoExceptionData $exceptionData): void
    {
        global $argv;
        $className = $exceptionData->getClassName();
        $originalMessage = $exceptionData->getOriginalMessageWithCodes();
        $trace = $exceptionData->getStackTrace();
        $traceString = $trace->getTraceAsString();
        $source = $trace->getFile() . ":" . $trace->getLine();
        $isErrorException = is_subclass_of($className, ErrorException::class);

        $message = "Exception: $originalMessage";
        $message .= "\n------------------------------------------------------------------------------------------------------------------------------------";

        if ($className && !$isErrorException) {
            $message .= "\nException: $className\nMessage: $originalMessage";
        } else {
            $message .= "\nError: $originalMessage";
        }

        if ($exceptionData->getSeverity()) {
            $message .= "\nSeverity: {$exceptionData->getSeverityAsString()}";
        }

        $message .= "\nLine: $source";

        if ($traceString) {
            $message .= "\nStack trace:\n$traceString";
        }
        if (!empty($argv)) {
            $message .= "\nargv = " . Objects::toString($argv);
        }
        $message .= "\n------------------------------------------------------------------------------------------------------------------------------------\n";
        echo $message;
    }
}