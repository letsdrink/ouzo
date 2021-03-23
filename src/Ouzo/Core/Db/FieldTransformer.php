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
    private string $field;
    private RelationFetcher $transformer;

    public function __construct(string $field, RelationFetcher $transformer)
    {
        $this->field = $field;
        $this->transformer = $transformer;
    }

    public function transform(array &$results): void
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
