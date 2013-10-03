<?php

namespace Ouzo\Db;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class FieldTransformer
{
    private $field;
    private $transformer;

    function __construct($field, $transformer)
    {
        $this->field = $field;
        $this->transformer = $transformer;
    }

    public function transform(&$results)
    {
        if ($this->field) {
            $fields = Arrays::map($results, Functions::extractFieldRecursively($this->field));
            $this->transformer->transform($fields);
        } else {
            $this->transformer->transform($results);
        }
    }
}