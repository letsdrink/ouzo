<?php

namespace Ouzo\Routing\Annotation\Route;

use Ouzo\Routing\Annotation\Route;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Delete extends Route
{
    public function __construct(array $data)
    {
        $data['methods'] = ['DELETE'];
        parent::__construct($data);
    }
}