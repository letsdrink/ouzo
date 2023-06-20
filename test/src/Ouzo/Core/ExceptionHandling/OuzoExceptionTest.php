<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\Tests\Assert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OuzoExceptionTest extends TestCase
{
    #[Test]
    public function shouldReturnErrorMessages()
    {
        //given
        $ouzoException = new OuzoException(500, "TestException", [new Error(0, "Error 1"), new Error(1, "Error 2")]);

        //when
        $messages = $ouzoException->getErrorMessages();

        //then
        Assert::thatArray($messages)
            ->containsOnly("Error 1", "Error 2");
    }
}
