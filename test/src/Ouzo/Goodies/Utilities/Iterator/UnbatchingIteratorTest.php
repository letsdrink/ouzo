<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Utilities\Iterator;


use PHPUnit\Framework\TestCase; 

class UnbatchingIteratorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFlattenChunks()
    {
        //given
        $iterator = new \ArrayIterator([[1, 2], [3, 4], [5]]);

        //when
        $unbatchedIterator = new UnbatchingIterator($iterator);

        //then
        $this->assertEquals([1, 2, 3, 4, 5], iterator_to_array($unbatchedIterator));
    }
}
