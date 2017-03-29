<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;

class Photo
{
    private $_photoName;
    private $_data;

    public function __construct($photoName, $data = '')
    {
        $this->_photoName = $photoName;
        $this->_data = $data;
    }

    public function getPhotoName()
    {
        return $this->_photoName;
    }
}

class PhotoFrame
{
    private $photo;

    public function __construct($photo)
    {
        $this->photo = $photo;
    }
}

class ArrayAssertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function containsShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(['1'])->contains('1');
        Assert::thatArray(['1', '2'])->contains('1');
        Assert::thatArray(['1', '2', '3'])->contains('1');
        Assert::thatArray(['1', '2', '3'])->contains('1', '2');
        Assert::thatArray(['1', '2', '3'])->contains('1', '2', '3');
        Assert::thatArray(['1', '2', '3'])->contains('3', '2', '1');
    }

    /**
     * @test
     */
    public function containsShouldAssertThatArrayContainsElementWithProperty()
    {
        $object = new stdClass();
        $object->prop = 1;

        Assert::thatArray([$object])->onProperty('prop')->contains(1);
    }

    /**
     * @test
     */
    public function shouldNotContainElementOpProperty()
    {
        $object = new stdClass();
        $object->prop = 2;

        CatchException::when(Assert::thatArray([$object])->onProperty('prop'))->contains(1);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldThrowException()
    {
        $this->_assertNotContains([null], '1');
        $this->_assertNotContains(['string'], '1');
        $this->_assertNotContains([['1', '2']], '3');
        $this->_assertNotContains([['1', '2']], '1', '3');
        $this->_assertNotContains([['1', '2']], '1', '2', '3');
    }

    /**
     * @test
     */
    public function hasSizeShouldAssertThatArrayHasSpecifiedSize()
    {
        Assert::thatArray([])->hasSize(0);
        Assert::thatArray(['1'])->hasSize(1);
        Assert::thatArray(['1', '2'])->hasSize(2);
    }

    /**
     * @test
     */
    public function hasSizeShouldThrowException()
    {
        $this->_assertNotHasSize([], 1);
        $this->_assertNotHasSize(['1'], 2);
        $this->_assertNotHasSize(['1', '2'], 0);
    }

    /**
     * @test
     */
    public function isEmptyShouldAssertThatArrayHasNoElements()
    {
        Assert::thatArray([])->isEmpty();
    }

    /**
     * @test
     */
    public function isEmptyShouldThrowException()
    {
        $this->_assertNotIsEmpty(['1']);
        $this->_assertNotIsEmpty(['1', '2']);
    }

    /**
     * @test
     */
    public function isNotEmptyShouldAssertThatArrayHasElements()
    {
        Assert::thatArray(['1'])->isNotEmpty();
        Assert::thatArray(['1', '2'])->isNotEmpty();
    }

    /**
     * @test
     */
    public function isNotEmptyShouldThrowException()
    {
        $this->_assertNotIsNotEmpty([]);
    }

    /**
     * @test
     */
    public function containsOnlyShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(['1'])->containsOnly('1');
        Assert::thatArray(['1', '2', '3'])->containsOnly('1', '2', '3');
        Assert::thatArray(['1', '2', '3'])->containsOnly('3', '1', '2');
    }

    /**
     * @test
     */
    public function containsOnlyShouldThrowException()
    {
        $this->_assertNotContainsOnly([null], '1');
        $this->_assertNotContainsOnly(['string'], '1');
        $this->_assertNotContainsOnly([['1', '2']], '3');
        $this->_assertNotContainsOnly([['1', '2']], '1', '3');
        $this->_assertNotContainsOnly([['1', '2']], '1', '2', '3');
        $this->_assertNotContainsOnly([['1', '2']], '1');
        $this->_assertNotContainsOnly([['1', '2', '3']], '1');
        $this->_assertNotContainsOnly([['1', '2', '3']], '1', '2');
    }

    /**
     * @test
     */
    public function containsExactlyShouldAssertThatArrayContainsElementInGivenOrder()
    {
        Assert::thatArray(['1'])->containsExactly('1');
        Assert::thatArray(['1', '2', '3'])->containsExactly('1', '2', '3');
    }

    /**
     * @test
     */
    public function containsExactlyShouldThrowException()
    {
        $this->_assertNotContainsExactly([null], '1');
        $this->_assertNotContainsExactly(['string'], '1');
        $this->_assertNotContainsExactly([['1', '2']], '3');
        $this->_assertNotContainsExactly([['1', '2']], '1', '3');
        $this->_assertNotContainsExactly([['1', '2']], '1', '2', '3');
        $this->_assertNotContainsExactly([['1', '2']], '1');
        $this->_assertNotContainsExactly([['1', '2', '3']], '1');
        $this->_assertNotContainsExactly([['1', '2', '3']], '1', '2');
        $this->_assertNotContainsExactly([['1', '2', '3']], '3', '1', '2');
    }

    /**
     * @test
     */
    public function containsKeyAndValueShouldAssertThatArrayContainsKeyValues()
    {
        $array = ['id' => 123, 'name' => 'john', 'surname' => 'smith'];
        Assert::thatArray($array)->containsKeyAndValue(['id' => 123, 'name' => 'john']);
    }

    /**
     * @test
     */
    public function containsKeyAndValueShouldThrowException()
    {
        $array = ['id' => 123, 'name' => 'john', 'surname' => 'smith'];
        $this->_assertNotContainsKeyAndValue($array,
            ['id' => 12]
        );
        $this->_assertNotContainsKeyAndValue($array,
            ['id' => 123, 'name' => 'john', 'surname' => 'smith', 'new_key' => 'new_value']
        );
    }

    /**
     * @test
     */
    public function containsShouldAssertThatArrayUsingOnMethod()
    {
        $photos[] = new Photo('photo1');
        $photos[] = new Photo('photo2');

        Assert::thatArray($photos)->onMethod('getPhotoName')->containsOnly('photo1', 'photo2');
    }

    /**
     * @test
     */
    public function containsShouldNotAssertThatArrayUsingOnMethod()
    {
        $photos[] = new Photo('photo1');
        $photos[] = new Photo('photo2');

        CatchException::when(Assert::thatArray($photos)->onMethod('getPhotoName'))->contains('photo3');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldCheckSequences()
    {
        $array = ['ccc', 'aaa', 'bbb', 'ccc', 'ddd'];
        Assert::thatArray($array)->containsSequence('ccc', 'ddd');
        Assert::thatArray($array)->containsSequence();
        Assert::thatArray($array)->containsSequence('aaa');
    }

    /**
     * @test
     */
    public function isEqualToShouldPassForEqualArrays()
    {
        $array = ['ccc', 'aaa'];
        Assert::thatArray($array)->isEqualTo(['ccc', 'aaa']);
    }

    /**
     * @test
     */
    public function isEqualToShouldThrowExceptionForDifferentArrays()
    {
        $array = ['ccc', 'aaa'];
        CatchException::when(Assert::thatArray($array))->isEqualTo('ddd', 'ccc');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenOrderIsIncorrect()
    {
        $array = ['ccc', 'aaa', 'bbb', 'ccc', 'ddd'];
        CatchException::when(Assert::thatArray($array))->containsSequence('ddd', 'ccc');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenIsNotSequence()
    {
        $array = ['ccc', 'aaa', 'bbb', 'ccc', 'ddd'];
        CatchException::when(Assert::thatArray($array))->containsSequence('aaa', 'ddd');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenPassTooManyParameters()
    {
        $array = ['ccc', 'aaa', 'bbb', 'ccc', 'ddd'];
        CatchException::when(Assert::thatArray($array))->containsSequence('ccc', 'aaa', 'bbb', 'ccc', 'ddd', 'zzz');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function excludesShouldThrowExceptionWhenFoundInArray()
    {
        CatchException::when(Assert::thatArray(['1', '2', '3', '4']))->excludes('7', '8', '4');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function excludeShouldCheckExclude()
    {
        Assert::thatArray(['1', '2', '3', '4'])->excludes('7', '8', '9');
        Assert::thatArray(['one', 'two', 'three', 'four'])->excludes('eleven');
    }

    /**
     * @test
     */
    public function shouldExtractPropertyRecursively()
    {
        $obj[0] = new stdClass();
        $obj[0]->property1 = new stdClass();
        $obj[0]->property1->name = 'name1';
        $obj[1] = new stdClass();
        $obj[1]->property1 = new stdClass();
        $obj[1]->property1->name = 'name2';

        Assert::thatArray($obj)->onProperty('property1->name')->containsExactly('name1', 'name2');
    }

    /**
     * @test
     */
    public function shouldExtractPrivateProperty()
    {
        $photos = [new Photo('vacation', 'vvv'), new Photo('portrait', 'ppp')];

        Assert::thatArray($photos)->onProperty('_data')->containsExactly('vvv', 'ppp');
    }

    /**
     * @test
     */
    public function shouldExtractPrivatePropertyRecursively()
    {
        $photos = [new PhotoFrame(new Photo('vacation', 'vvv'))];

        Assert::thatArray($photos)->onProperty('photo->_data')->containsExactly('vvv');
    }

    /**
     * @test
     */
    public function onPropertyFailureShouldShowNiceMessage()
    {
        //given
        $obj[0] = new stdClass();
        $obj[0]->property1 = 'prop1';
        $obj[1] = new stdClass();
        $obj[1]->property1 = 'prop2';

        //when
        CatchException::when(Assert::thatArray($obj)->onProperty('property1'))->contains('prop3');

        //then
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException')
            ->hasMessage('Cannot find expected ["prop3"] in actual ["prop1", "prop2"]');
    }

    /**
     * @test
     */
    public function shouldCheckKeysRecursivelyAreEqual()
    {
        //given
        $array = [
            'customer' => [
                'name' => 'Name',
                'phone' => '123456789',
            ],
            'other' => [
                'ids_map' => [
                    '1qaz' => 'qaz',
                    '2wsx' => 'wsx'
                ]
            ]
        ];

        //then
        $expected = [
            'customer' => [
                'name' => 'New name',
                'phone' => '45456456',
            ],
            'other' => [
                'ids_map' => [
                    '1qaz' => 'QQQ',
                    '2wsx' => 'EVV'
                ]
            ]
        ];
        Assert::thatArray($array)->hasEqualKeysRecursively($expected);
    }

    /**
     * @test
     */
    public function shouldAssertUsingExtractingForMultipleSelectors()
    {
        $photos[] = new Photo('photo1', 'd1');
        $photos[] = new Photo('photo2', 'd2');

        Assert::thatArray($photos)->extracting('getPhotoName()', '_data')->contains(['photo1', 'd1'], ['photo2', 'd2']);
    }

    /**
     * @test
     */
    public function shouldAssertUsingExtractingForSingleSelector()
    {
        $photos[] = new Photo('photo1', 'd1');
        $photos[] = new Photo('photo2', 'd2');

        Assert::thatArray($photos)->extracting('_data')->contains('d1', 'd2');
    }

    /**
     * @test
     */
    public function shouldAssertUsingKeys()
    {
        // given
        $actual = ['14' => true, '15' => true];

        // when
        Assert::thatArray($actual)->keys()->contains('14', '15');
    }

    private function _assertNot()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $array = array_shift($args);

        call_user_func_array([CatchException::when(Assert::thatArray($array)), $method], $args);
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    private function _assertNotContains()
    {
        call_user_func_array([$this, '_assertNot'], array_merge(['contains'], func_get_args()));
    }

    private function _assertNotContainsOnly()
    {
        call_user_func_array([$this, '_assertNot'], array_merge(['containsOnly'], func_get_args()));
    }

    private function _assertNotContainsExactly()
    {
        call_user_func_array([$this, '_assertNot'], array_merge(['containsExactly'], func_get_args()));
    }

    private function _assertNotIsEmpty()
    {
        call_user_func_array([$this, '_assertNot'], array_merge(['isEmpty'], func_get_args()));
    }

    private function _assertNotIsNotEmpty()
    {
        call_user_func_array([$this, '_assertNot'], array_merge(['isNotEmpty'], func_get_args()));
    }

    private function _assertNotHasSize()
    {
        call_user_func_array([$this, '_assertNot'], array_merge(['hasSize'], func_get_args()));
    }

    private function _assertNotContainsKeyAndValue()
    {
        call_user_func_array([$this, '_assertNot'], array_merge(['containsKeyAndValue'], func_get_args()));
    }
}
