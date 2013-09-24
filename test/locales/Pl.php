<?php
class Pl 
{
    static $labels = array(
        'product' => array(
            'description' => 'Product description'
        )
    );

    static function getLabels()
    {
        return self::$labels;
    }
}