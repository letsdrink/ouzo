<?php

namespace Ouzo\Db;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
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
            $fields = FluentArray::from($results)
                ->map(Functions::extractFieldRecursively($this->field))
                ->filter(Functions::notEmpty())
                ->toArray();
            $this->transformer->transform($fields);
        } else {
            $this->transformer->transform($results);
        }
    }
}