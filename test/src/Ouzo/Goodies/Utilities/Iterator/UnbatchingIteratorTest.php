<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Utilities\Iterator;


class UnbatchingIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldFlattenChunks()
    {
        //given
        $iterator = new \ArrayIterator(array(array(1, 2), array(3, 4), array(5)));

        //when
        $unbatchedIterator = new UnbatchingIterator($iterator);

        //then
        $this->assertEquals(array(1, 2, 3, 4, 5), iterator_to_array($unbatchedIterator));
    }
}
