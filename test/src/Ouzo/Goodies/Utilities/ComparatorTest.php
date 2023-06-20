<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\Functions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

interface Foo
{
    public function method();
}

class CallableWrapper
{
    public function __construct(private object $callable)
    {
    }

    public function call(): void
    {
        call_user_func_array($this->callable, func_get_args());
    }
}

class ComparatorTest extends TestCase
{
    #[Test]
    public function shouldCompareByExpression()
    {
        //given
        $mock1 = Mock::mock('Foo');
        Mock::when($mock1)->method()->thenReturn(1);
        $mock2 = Mock::mock('Foo');
        Mock::when($mock2)->method()->thenReturn(2);
        $comparator = Comparator::compareBy('method()');

        //when
        $lesser = $comparator($mock1, $mock2);
        $greater = $comparator($mock2, $mock1);
        $equal = $comparator($mock1, $mock1);

        //then
        $this->assertEquals(-1, $lesser);
        $this->assertEquals(1, $greater);
        $this->assertEquals(0, $equal);
    }

    #[Test]
    public function shouldProperlyCompareUsingReversed()
    {
        //given
        $reversed = Comparator::reverse(Comparator::natural());

        //when
        $greater = $reversed(1, 2);
        $lesser = $reversed(2, 1);
        $equal = $reversed(1, 1);

        //then
        $this->assertEquals(-1, $lesser);
        $this->assertEquals(1, $greater);
        $this->assertEquals(0, $equal);
    }

    #[Test]
    public function shouldProperlyCompareUsingDefault()
    {
        //given
        $reversed = Comparator::natural();

        //when
        $greater = $reversed(2, 1);
        $lesser = $reversed(1, 2);
        $equal = $reversed(1, 1);

        //then
        $this->assertEquals(-1, $lesser);
        $this->assertEquals(1, $greater);
        $this->assertEquals(0, $equal);
    }

    #[Test]
    public function shouldNotInvokeTieBreakersWhenMainComparatorResolves()
    {
        //given
        $tieBreaker = Functions::throwException(new Exception('should not be invoked'));
        $alwaysLess = Functions::constant(-1);
        $comparator = Comparator::compound($alwaysLess, $tieBreaker);

        //when
        CatchException::when(new CallableWrapper($comparator))->call(null, null);

        //then
        CatchException::assertThat()->notCaught();
    }

    #[Test]
    public function shouldInvokeFirstTieBreaker()
    {
        //given
        $tieBreaker2 = Functions::throwException(new Exception('second should not be invoked'));
        $alwaysEqual = Functions::constant(0);
        $alwaysLess = Functions::constant(-1);
        $comparator = Comparator::compound($alwaysEqual, $alwaysLess, $tieBreaker2);

        //when
        CatchException::when(new CallableWrapper($comparator))->call(null, null);

        //then
        CatchException::assertThat()->notCaught();
    }

    #[Test]
    public function shouldInvokeFirstLastTieBreaker()
    {
        //given
        $tieBreaker2 = Functions::throwException(new Exception('second should be invoked'));
        $alwaysEqual = Functions::constant(0);
        $comparator = Comparator::compound($alwaysEqual, $alwaysEqual, $tieBreaker2);

        //when
        CatchException::when(new CallableWrapper($comparator))->call(null, null);

        //then
        CatchException::assertThat()->hasMessage('second should be invoked');
    }

    #[Test]
    public function shouldUseFinalTieBreaker()
    {
        //given
        $alwaysLess = Functions::constant(-1);
        $alwaysGreater = Functions::constant(1);
        $alwaysEqual = Functions::constant(0);
        $lesserComparator = Comparator::compound($alwaysEqual, $alwaysEqual, $alwaysLess);
        $greaterComparator = Comparator::compound($alwaysEqual, $alwaysEqual, $alwaysGreater);
        $equalComparator = Comparator::compound($alwaysEqual, $alwaysEqual, $alwaysEqual);

        //when
        $greater = $greaterComparator(null, null);
        $lesser = $lesserComparator(null, null);
        $equal = $equalComparator(null, null);

        //then
        $this->assertEquals(-1, $lesser);
        $this->assertEquals(1, $greater);
        $this->assertEquals(0, $equal);
    }

    #[Test]
    public function shouldNotUseFinalTieBreakerWhenAnyPriorResolves()
    {
        //given
        $alwaysLess = Functions::constant(-1);
        $alwaysGreater = Functions::constant(1);
        $alwaysEqual = Functions::constant(0);
        $greaterComparator = Comparator::compound($alwaysEqual, $alwaysGreater, $alwaysLess);
        $lesserComparator1 = Comparator::compound($alwaysEqual, $alwaysLess, $alwaysGreater);
        $lesserComparator2 = Comparator::compound($alwaysLess, $alwaysEqual, $alwaysEqual);

        //when
        $greater = $greaterComparator(null, null);
        $lesser1 = $lesserComparator1(null, null);
        $lesser2 = $lesserComparator2(null, null);

        //then
        $this->assertEquals(-1, $lesser1);
        $this->assertEquals(1, $greater);
        $this->assertEquals(-1, $lesser2);
    }
}
