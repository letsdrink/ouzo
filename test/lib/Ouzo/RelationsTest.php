<?php

namespace Ouzo;


use InvalidArgumentException;

class RelationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionIfDuplicatedRelation()
    {
        //given
        $params = array(
            'hasOne' => array(
                'category' => array('class' => 'Category', 'foreignKey' => 'id_category')
            ),
            'belongsTo' => array('category' => array('class' => 'OrderProduct'))
        );

        //when
        try {
            new Relations("class", $params, "id");
            $this->fail();
        } //then
        catch (InvalidArgumentException $e) {
        }
    }
}
