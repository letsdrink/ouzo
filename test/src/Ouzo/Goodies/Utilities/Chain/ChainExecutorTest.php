<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Chain\ChainExecutor;
use PHPUnit\Framework\TestCase;

class ChainExecutorTest extends TestCase
{
    /**
     * @test
     */
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

    /**
     * @test
     */
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
