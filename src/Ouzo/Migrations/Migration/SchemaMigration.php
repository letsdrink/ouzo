<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Migration;

use Ouzo\Model;

class SchemaMigration extends Model
{
    public static $db;

    public function __construct($attributes = [])
    {
        parent::__construct([
            'db' => self::$db,
            'attributes' => $attributes,
            'fields' => ['version', 'applied_at']]);
    }
}