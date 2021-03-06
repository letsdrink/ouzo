<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\CatchExceptionAssert;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

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

class ArrayAssertTest extends TestCase
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
        //given
        $object = new stdClass();
        $object->prop = 2;

        //when
        $this->assertNot(fn() => Assert::thatArray([$object])->onProperty('prop')->contains(1));
    }

    /**
     * @test
     */
    public function containsShouldThrowException()
    {
        $this->assertNot(fn() => Assert::thatArray([null])->contains('1'));
        $this->assertNot(fn() => Assert::thatArray(['string'])->contains('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->contains('3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->contains('1', '3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->contains('1', '2', '3'));
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
        $this->assertNot(fn() => Assert::thatArray([])->hasSize(1));
        $this->assertNot(fn() => Assert::thatArray(['1'])->hasSize(2));
        $this->assertNot(fn() => Assert::thatArray(['1', '2'])->hasSize(0));
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
        $this->assertNot(fn() => Assert::thatArray(['1', '2'])->isEmpty());
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
        $this->assertNot(fn() => Assert::thatArray([])->isNotEmpty());
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
        $this->assertNot(fn() => Assert::thatArray([null])->containsOnly('1'));
        $this->assertNot(fn() => Assert::thatArray(['string'])->containsOnly('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsOnly('3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsOnly('1', '3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsOnly('1', '2', '3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsOnly('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2', '3']])->containsOnly('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2', '3']])->containsOnly('1', '2'));
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
        $this->assertNot(fn() => Assert::thatArray([null])->containsExactly('1'));
        $this->assertNot(fn() => Assert::thatArray(['string'])->containsExactly('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsExactly('3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsExactly('1', '3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsExactly('1', '2', '3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->containsExactly('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2', '3']])->containsExactly('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2', '3']])->containsExactly('1', '2'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2', '3']])->containsExactly('3', '1', '2'));
    }

    /**
     * @test
     */
    public function containsKeyAndValueShouldAssertThatArrayContainsKeyValues()
    {
        //given
        $array = ['id' => 123, 'name' => 'john', 'surname' => 'smith'];

        //when
        Assert::thatArray($array)->containsKeyAndValue(['id' => 123, 'name' => 'john']);
    }

    /**
     * @test
     */
    public function containsKeyAndValueShouldThrowException()
    {
        //given
        $haystack = ['id' => 123, 'name' => 'john', 'surname' => 'smith'];

        //then
        $this->assertNot(fn() => Assert::thatArray($haystack)->containsKeyAndValue(['id' => 12]));
        $this->assertNot(fn() => Assert::thatArray($haystack)->containsKeyAndValue(['id' => 123, 'name' => 'john', 'surname' => 'smith', 'new_key' => 'new_value']));
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

        $this->assertNot(fn() => Assert::thatArray($photos)->onMethod('getPhotoName')->contains('photo3'));
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
        Assert::thatArray(['ccc', 'aaa'])->isEqualTo(['ccc', 'aaa']);
    }

    /**
     * @test
     */
    public function isEqualToShouldThrowExceptionForDifferentArrays()
    {
        $this->assertNot(fn() => Assert::thatArray(['ccc', 'aaa'])->isEqualTo(['ddd', 'ccc']));
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenOrderIsIncorrect()
    {
        $this->assertNot(fn() => Assert::thatArray(['ccc', 'aaa', 'bbb', 'ccc', 'ddd'])->containsSequence('ddd', 'ccc'));
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenIsNotSequence()
    {
        $this->assertNot(fn() => Assert::thatArray(['ccc', 'aaa', 'bbb', 'ccc', 'ddd'])->containsSequence('aaa', 'ddd'));
    }

    /**
     * @test
     */
    public function containsShouldThrowExceptionWhenPassTooManyParameters()
    {
        //given
        $haystack = ['ccc', 'aaa', 'bbb', 'ccc', 'ddd'];

        //then
        $this->assertNot(fn() => Assert::thatArray($haystack)->containsSequence('ccc', 'aaa', 'bbb', 'ccc', 'ddd', 'zzz'));
    }

    /**
     * @test
     */
    public function excludesShouldThrowExceptionWhenFoundInArray()
    {
        $this->assertNot(fn() => Assert::thatArray(['1', '2', '3', '4'])->excludes('7', '8', '4'));
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
        $this->assertNot(fn() => Assert::thatArray($obj)->onProperty('property1')->contains('prop3'))
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
        Assert::thatArray($array)->hasEqualKeysRecursively([
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
        ]);
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
        //given
        $photos[] = new Photo('photo1', 'd1');
        $photos[] = new Photo('photo2', 'd2');

        //then
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

    private function assertNot(Closure $closure): CatchExceptionAssert
    {
        try {
            $closure();
        } catch (Throwable $throwable) {
            return (new CatchExceptionAssert($throwable))->isInstanceOf(ExpectationFailedException::class);
        }
        return new CatchExceptionAssert(null);
    }
}
