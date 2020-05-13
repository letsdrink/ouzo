<?php

namespace Ouzo\Routing\Annotation\Route;

use Ouzo\Routing\Annotation\Route;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Get extends Route
{
    public function __construct(array $data)
    {
        $data['methods'] = ['GET'];
        parent::__construct($data);
    }
}