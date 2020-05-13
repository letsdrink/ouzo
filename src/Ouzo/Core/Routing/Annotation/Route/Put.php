<?php

namespace Ouzo\Routing\Annotation\Route;

use Ouzo\Routing\Annotation\Route;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Put extends Route
{
    public function __construct(array $data)
    {
        $data['methods'] = ['PUT'];
        parent::__construct($data);
    }
}