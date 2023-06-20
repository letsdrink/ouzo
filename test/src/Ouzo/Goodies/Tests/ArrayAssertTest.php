<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchExceptionAssert;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class Photo
{
    public function __construct(
        private string $photoName,
        private string $data = ''
    )
    {
    }

    public function getPhotoName(): string
    {
        return $this->photoName;
    }
}

class PhotoFrame
{
    public function __construct(private Photo $photo)
    {
    }
}

class ArrayAssertTest extends TestCase
{
    #[Test]
    public function containsShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(['1'])->contains('1');
        Assert::thatArray(['1', '2'])->contains('1');
        Assert::thatArray(['1', '2', '3'])->contains('1');
        Assert::thatArray(['1', '2', '3'])->contains('1', '2');
        Assert::thatArray(['1', '2', '3'])->contains('1', '2', '3');
        Assert::thatArray(['1', '2', '3'])->contains('3', '2', '1');
    }

    #[Test]
    public function containsShouldAssertThatArrayContainsElementWithProperty()
    {
        $object = new stdClass();
        $object->prop = 1;

        Assert::thatArray([$object])->onProperty('prop')->contains(1);
    }

    #[Test]
    public function shouldNotContainElementOpProperty()
    {
        //given
        $object = new stdClass();
        $object->prop = 2;

        //when
        $this->assertNot(fn() => Assert::thatArray([$object])->onProperty('prop')->contains(1));
    }

    #[Test]
    public function containsShouldThrowException()
    {
        $this->assertNot(fn() => Assert::thatArray([null])->contains('1'));
        $this->assertNot(fn() => Assert::thatArray(['string'])->contains('1'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->contains('3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->contains('1', '3'));
        $this->assertNot(fn() => Assert::thatArray([['1', '2']])->contains('1', '2', '3'));
    }

    #[Test]
    public function hasSizeShouldAssertThatArrayHasSpecifiedSize()
    {
        Assert::thatArray([])->hasSize(0);
        Assert::thatArray(['1'])->hasSize(1);
        Assert::thatArray(['1', '2'])->hasSize(2);
    }

    #[Test]
    public function hasSizeShouldThrowException()
    {
        $this->assertNot(fn() => Assert::thatArray([])->hasSize(1));
        $this->assertNot(fn() => Assert::thatArray(['1'])->hasSize(2));
        $this->assertNot(fn() => Assert::thatArray(['1', '2'])->hasSize(0));
    }

    #[Test]
    public function isEmptyShouldAssertThatArrayHasNoElements()
    {
        Assert::thatArray([])->isEmpty();
    }

    #[Test]
    public function isEmptyShouldThrowException()
    {
        $this->assertNot(fn() => Assert::thatArray(['1', '2'])->isEmpty());
    }

    #[Test]
    public function isNotEmptyShouldAssertThatArrayHasElements()
    {
        Assert::thatArray(['1'])->isNotEmpty();
        Assert::thatArray(['1', '2'])->isNotEmpty();
    }

    #[Test]
    public function isNotEmptyShouldThrowException()
    {
        $this->assertNot(fn() => Assert::thatArray([])->isNotEmpty());
    }

    #[Test]
    public function containsOnlyShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(['1'])->containsOnly('1');
        Assert::thatArray(['1', '2', '3'])->containsOnly('1', '2', '3');
        Assert::thatArray(['1', '2', '3'])->containsOnly('3', '1', '2');
    }

    #[Test]
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

    #[Test]
    public function containsExactlyShouldAssertThatArrayContainsElementInGivenOrder()
    {
        Assert::thatArray(['1'])->containsExactly('1');
        Assert::thatArray(['1', '2', '3'])->containsExactly('1', '2', '3');
    }

    #[Test]
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

    #[Test]
    public function containsKeyAndValueShouldAssertThatArrayContainsKeyValues()
    {
        //given
        $array = ['id' => 123, 'name' => 'john', 'surname' => 'smith'];

        //when
        Assert::thatArray($array)->containsKeyAndValue(['id' => 123, 'name' => 'john']);
    }

    #[Test]
    public function containsKeyAndValueShouldThrowException()
    {
        //given
        $haystack = ['id' => 123, 'name' => 'john', 'surname' => 'smith'];

        //then
        $this->assertNot(fn() => Assert::thatArray($haystack)->containsKeyAndValue(['id' => 12]));
        $this->assertNot(fn() => Assert::thatArray($haystack)->containsKeyAndValue(['id' => 123, 'name' => 'john', 'surname' => 'smith', 'new_key' => 'new_value']));
    }

    #[Test]
    public function containsShouldAssertThatArrayUsingOnMethod()
    {
        $photos[] = new Photo('photo1');
        $photos[] = new Photo('photo2');

        Assert::thatArray($photos)->onMethod('getPhotoName')->containsOnly('photo1', 'photo2');
    }

    #[Test]
    public function containsShouldNotAssertThatArrayUsingOnMethod()
    {
        $photos[] = new Photo('photo1');
        $photos[] = new Photo('photo2');

        $this->assertNot(fn() => Assert::thatArray($photos)->onMethod('getPhotoName')->contains('photo3'));
    }

    #[Test]
    public function containsShouldCheckSequences()
    {
        $array = ['ccc', 'aaa', 'bbb', 'ccc', 'ddd'];
        Assert::thatArray($array)->containsSequence('ccc', 'ddd');
        Assert::thatArray($array)->containsSequence();
        Assert::thatArray($array)->containsSequence('aaa');
    }

    #[Test]
    public function isEqualToShouldPassForEqualArrays()
    {
        Assert::thatArray(['ccc', 'aaa'])->isEqualTo(['ccc', 'aaa']);
    }

    #[Test]
    public function isEqualToShouldThrowExceptionForDifferentArrays()
    {
        $this->assertNot(fn() => Assert::thatArray(['ccc', 'aaa'])->isEqualTo(['ddd', 'ccc']));
    }

    #[Test]
    public function containsShouldThrowExceptionWhenOrderIsIncorrect()
    {
        $this->assertNot(fn() => Assert::thatArray(['ccc', 'aaa', 'bbb', 'ccc', 'ddd'])->containsSequence('ddd', 'ccc'));
    }

    #[Test]
    public function containsShouldThrowExceptionWhenIsNotSequence()
    {
        $this->assertNot(fn() => Assert::thatArray(['ccc', 'aaa', 'bbb', 'ccc', 'ddd'])->containsSequence('aaa', 'ddd'));
    }

    #[Test]
    public function containsShouldThrowExceptionWhenPassTooManyParameters()
    {
        //given
        $haystack = ['ccc', 'aaa', 'bbb', 'ccc', 'ddd'];

        //then
        $this->assertNot(fn() => Assert::thatArray($haystack)->containsSequence('ccc', 'aaa', 'bbb', 'ccc', 'ddd', 'zzz'));
    }

    #[Test]
    public function excludesShouldThrowExceptionWhenFoundInArray()
    {
        $this->assertNot(fn() => Assert::thatArray(['1', '2', '3', '4'])->excludes('7', '8', '4'));
    }

    #[Test]
    public function excludeShouldCheckExclude()
    {
        Assert::thatArray(['1', '2', '3', '4'])->excludes('7', '8', '9');
        Assert::thatArray(['one', 'two', 'three', 'four'])->excludes('eleven');
    }

    #[Test]
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

    #[Test]
    public function shouldExtractPrivateProperty()
    {
        $photos = [new Photo('vacation', 'vvv'), new Photo('portrait', 'ppp')];

        Assert::thatArray($photos)->onProperty('data')->containsExactly('vvv', 'ppp');
    }

    #[Test]
    public function shouldExtractPrivatePropertyRecursively()
    {
        $photos = [new PhotoFrame(new Photo('vacation', 'vvv'))];

        Assert::thatArray($photos)->onProperty('photo->data')->containsExactly('vvv');
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldAssertUsingExtractingForMultipleSelectors()
    {
        $photos[] = new Photo('photo1', 'd1');
        $photos[] = new Photo('photo2', 'd2');

        Assert::thatArray($photos)->extracting('getPhotoName()', 'data')->contains(['photo1', 'd1'], ['photo2', 'd2']);
    }

    #[Test]
    public function shouldAssertUsingExtractingForSingleSelector()
    {
        //given
        $photos[] = new Photo('photo1', 'd1');
        $photos[] = new Photo('photo2', 'd2');

        //then
        Assert::thatArray($photos)->extracting('data')->contains('d1', 'd2');
    }

    #[Test]
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
