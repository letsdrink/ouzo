<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Chain\ChainExecutor;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ChainExecutorTest extends TestCase
{
    #[Test]
    public function shouldExecuteChain()
    {
        //given
        $chainExecutor = new ChainExecutor();
        $chainExecutor->add(new InterceptorOne());
        $chainExecutor->add(new InterceptorTwo());

        //when
        $result = $chainExecutor->execute('', fn($param) => $param . ' execution');

        //then
        $this->assertEquals('12 execution', $result);
    }

    #[Test]
    public function shouldExecuteBeforeAndAfterInterceptor()
    {
        //given
        $chainExecutor = new ChainExecutor();
        $chainExecutor->add(new BeforeAfterInterceptor());

        //when
        $result = $chainExecutor->execute('', fn($param) => $param . ' execution ');

        //then
        $this->assertEquals('before execution after', $result);
    }
}
