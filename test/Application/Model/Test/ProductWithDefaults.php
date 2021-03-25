<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Application\Model\Test;

use Ouzo\Model;

/**
 * @property string description
 * @property string name
 * @property Category category
 */
class ProductWithDefaults extends Model
{
    public static string $defaultName = 'no name';
    public static string $defaultDescription = 'no desc';

    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'table' => 'products',
            'attributes' => $attributes,
            'fields' => [
                'description' => self::$defaultDescription,
                'name' => fn() => ProductWithDefaults::$defaultName,
                'id_category',
                'id_manufacturer',
                'sale'
            ]]);
    }
}
