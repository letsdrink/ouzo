<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

class SchemaMigration extends Model
{
    public function __construct($attributes = [])
    {
        parent::__construct([
            'attributes' => $attributes,
            'fields' => ['version', 'applied_at']]);
    }
}