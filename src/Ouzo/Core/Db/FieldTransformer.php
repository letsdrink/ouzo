<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

class FieldTransformer
{
    private $field;

    /** @var RelationFetcher */
    private $transformer;

    public function __construct($field, RelationFetcher $transformer)
    {
        $this->field = $field;
        $this->transformer = $transformer;
    }

    public function transform(&$results)
    {
        if ($this->field) {
            $fields = FluentArray::from($results)
                ->map(Functions::extractExpression($this->field))
                ->flatten()
                ->filterNotBlank()
                ->toArray();
            $this->transformer->transform($fields);
        } else {
            $this->transformer->transform($results);
        }
    }
}
