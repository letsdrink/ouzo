<?php

namespace Ouzo;


class PluralizeOption {

    private $value;

    function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}