<?php

namespace Ouzo\Routing\Annotation\Route;

use Ouzo\Routing\Annotation\Route;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Post extends Route
{
    public function __construct(array $data)
    {
        $data['methods'] = ['POST'];
        parent::__construct($data);
    }
}