<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Whoops\Handler\Handler;

class DebugErrorLogHandler extends Handler
{
    public function handle(): ?int
    {
        $exception = $this->getInspector()->getException();
        $ouzoExceptionData = OuzoExceptionData::forException(500, $exception);
        ExceptionLogger::newInstance($ouzoExceptionData)->log();
        return Handler::DONE;
    }
}