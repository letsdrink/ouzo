<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Session;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
    }

    #[Test]
    public function shouldSetSessionValue()
    {
        //when
        Session::set('key', 'value');

        //then
        Assert::thatSession()
            ->hasSize(1)
            ->containsKeyAndValue(['key' => 'value']);
    }

    #[Test]
    public function shouldSetNestedSessionValue()
    {
        //when
        Session::set('key1', 'key2', 'value');

        //then
        Assert::thatSession()->hasSize(1);
        $this->assertEquals('value', $_SESSION['key1']['key2']);
    }

    #[Test]
    public function shouldSetMultipleSessionValues()
    {
        //when
        Session::set('key1', 'value1')
            ->set('key2', 'value2')
            ->set('key3', 'value3');

        //then
        Assert::thatSession()
            ->hasSize(3)
            ->containsKeyAndValue([
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3'
            ]);
    }

    #[Test]
    public function shouldGetSessionValue()
    {
        //given
        $_SESSION['key'] = 'value';

        //when
        $value = Session::get('key');

        //then
        $this->assertEquals('value', $value);
    }

    #[Test]
    public function shouldGetNestedSessionValue()
    {
        //given
        $_SESSION['key1']['key2'] = 'value';

        //when
        $value = Session::get('key1', 'key2');

        //then
        $this->assertEquals('value', $value);
    }

    #[Test]
    public function getShouldReturnNullIfKeyDoesNotExist()
    {
        //when
        $value = Session::get('key');

        //then
        $this->assertNull($value);
    }

    #[Test]
    public function hasShouldReturnTrueIfItemExistsInSession()
    {
        //given
        Session::set('key', 'value');

        //when
        $value = Session::has('key');

        //then
        $this->assertTrue($value);
    }

    #[Test]
    public function hasShouldReturnFalseIfItemDoesNotExistInSession()
    {
        //when
        $value = Session::has('key');

        //then
        $this->assertFalse($value);
    }

    #[Test]
    public function shouldFlushSession()
    {
        //given
        Session::set('key', 'value');

        //when
        Session::flush();

        //then
        Assert::thatSession()->isEmpty();
    }

    #[Test]
    public function shouldFlushIfSessionIsEmpty()
    {
        //when
        Session::flush();

        //then
        Assert::thatSession()->isEmpty();
    }

    #[Test]
    public function shouldRemoveElementFromSession()
    {
        //given
        Session::set('key1', 'value1');
        Session::set('key2', 'value2');

        //when
        Session::remove('key1');

        //then
        Assert::thatSession()
            ->hasSize('1')
            ->containsKeyAndValue(['key2' => 'value2']);
    }

    #[Test]
    public function removeShouldDoNothingIfElementDoesNotExist()
    {
        //when
        Session::remove('key1');

        //then
        Assert::thatSession()->isEmpty();
    }

    #[Test]
    public function shouldGetAllValuesFromSession()
    {
        //given
        Session::set('key', 'value');

        //when
        $all = Session::all();

        //then
        Assert::thatArray($all)
            ->hasSize(1)
            ->containsKeyAndValue(['key' => 'value']);
    }

    #[Test]
    public function shouldPushSessionValue()
    {
        //when
        Session::push('key', 'value');

        //then
        Assert::thatSession()->hasSize(1);

        $value = Session::get('key');
        Assert::thatArray($value)->containsExactly('value');
    }

    #[Test]
    public function shouldPushNestedSessionValue()
    {
        //when
        Session::push('key1', 'key2', 'value');

        //then
        Assert::thatSession()->hasSize(1);

        $value = Session::get('key1', 'key2');
        Assert::thatArray($value)->containsExactly('value');
    }

    #[Test]
    public function shouldPushSessionValueWhenArrayIsNotEmpty()
    {
        // given
        Session::push('key', 'value1');
        Session::push('key', 'value2');

        //when
        Session::push('key', 'value3');

        //then
        Assert::thatSession()->hasSize(1);

        $value = Session::get('key');
        Assert::thatArray($value)->containsExactly('value1', 'value2', 'value3');
    }

    #[Test]
    public function shouldPushNestedSessionValueWhenArrayIsNotEmpty()
    {
        // given
        Session::push('key1', 'key2', 'value1');
        Session::push('key1', 'key2', 'value2');

        //when
        Session::push('key1', 'key2', 'value3');

        //then
        Assert::thatSession()->hasSize(1);

        $value = Session::get('key1', 'key2');
        Assert::thatArray($value)->containsExactly('value1', 'value2', 'value3');
    }
}
