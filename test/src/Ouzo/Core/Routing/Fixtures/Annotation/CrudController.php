<?php

namespace Application\Model\Test;

use Ouzo\Routing\Annotation\Route;

class CrudController
{
    /**
     * @Route\Post("/create")
     */
    public function post() {}

    /**
     * @Route\Get("/read")
     */
    public function get() {}

    /**
     * @Route\Put("/update")
     */
    public function put() {}

    /**
     * @Route\Delete("/delete")
     */
    public function delete() {}
}