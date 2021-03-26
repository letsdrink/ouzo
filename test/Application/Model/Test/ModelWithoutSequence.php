<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Model;

class ModelWithoutSequence extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'table' => 'products',
            'primaryKey' => 'id',
            'fields' => ['name'],
            'sequence' => '',
            'attributes' => $attributes
        ]);
    }
}
