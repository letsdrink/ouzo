<?php
namespace Ouzo\Utilities\Iterator;

use ArrayIterator;
use PHPUnit_Framework_TestCase;

class BatchingIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldChunkElementsWhenLengthDivisibleByChunk()
    {
        //given
        $array = array(1, 2, 3, 4);
        $batchIterator = new BatchingIterator(new ArrayIterator($array), 2);
        $result = array();

        //when
        foreach ($batchIterator as $key => $value) {
            $result[$key] = $value;
        }

        //then
        $this->assertEquals(array(array(1, 2), array(3, 4)), $result);
    }

    /**
     * @test
     */
    public function shouldChunkElementsWhenLengthNotDivisibleByChunk()
    {
        //given
        $array = array(1, 2, 3);
        $batchIterator = new BatchingIterator(new ArrayIterator($array), 2);
        $result = array();

        //when
        foreach ($batchIterator as $key => $value) {
            $result[$key] = $value;
        }

        //then
        $this->assertEquals(array(array(1, 2), array(3)), $result);
    }

    /**
     * @test
     */
    public function shouldNotBeValidForEmptyArray()
    {
        //given
        $batchIterator = new BatchingIterator(new ArrayIterator([]), 2);

        //when
        $valid = $batchIterator->valid();

        //then
        $this->assertFalse($valid);
    }

    /**
     * @test
     */
    public function shouldRewindIterator()
    {
        $ait = new ArrayIterator(array('a', 'b', 'c', 'd'));
        $ait->next();
        $ait->next();
        $batchIterator = new BatchingIterator($ait, 2);

        //when
        $batchIterator->rewind();

        //then
        $this->assertEquals(array(array('a', 'b'), array('c', 'd')), iterator_to_array($batchIterator));
    }
}
