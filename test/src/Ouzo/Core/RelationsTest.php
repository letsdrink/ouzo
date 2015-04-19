<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Relations;

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
