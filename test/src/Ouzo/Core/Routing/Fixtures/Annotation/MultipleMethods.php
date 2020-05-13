<?php

namespace Application\Model\Test;

use Ouzo\Routing\Annotation\Route;

class MultipleMethods
{
    /**
     * @Route\Get("/get")
     * @Route\Post("/post")
     */
    public function getAndPost() {}

}