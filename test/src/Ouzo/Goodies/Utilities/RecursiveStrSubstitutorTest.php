<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\RecursiveStrSubstitutor;
use PHPUnit\Framework\TestCase;

class RecursiveStrSubstitutorTest extends TestCase
{
    #[Test]
    public function shouldSubstituteNormalValues()
    {
        //given
        $strSubstitutor = new RecursiveStrSubstitutor(['NAME' => 'John', 'SURNAME' => 'Smith']);

        //when
        $substituted = $strSubstitutor->replace('Hi {{NAME}} {{SURNAME}}');

        //then
        $this->assertEquals('Hi John Smith', $substituted);
    }

    #[Test]
    public function shouldSubstituteRecursively()
    {
        //given
        $strSubstitutor = new RecursiveStrSubstitutor(['URL' => '{{HOST}}', 'HOST' => 'ouzoframework.org']);

        //when
        $substituted = $strSubstitutor->replace('Best website: {{URL}}');

        //then
        $this->assertEquals('Best website: ouzoframework.org', $substituted);
    }

    #[Test]
    public function shouldReturnValueWhenInfinityLoopOccurs()
    {
        //given
        $strSubstitutor = new RecursiveStrSubstitutor(['URL' => '{{HOST}}', 'HOST' => '{{URL}}'], null, 10);

        //when
        $substituted = $strSubstitutor->replace('Best website: {{URL}}');

        //then
        $this->assertEquals('Best website: {{URL}}', $substituted);
    }
}
