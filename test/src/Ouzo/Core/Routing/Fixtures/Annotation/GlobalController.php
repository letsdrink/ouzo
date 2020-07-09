<?php

namespace Application\Model\Test;

use Ouzo\Routing\Annotation\Route;

/**
 * @Route("/prefix")
 */
class GlobalController
{
    /**
     * @Route\Get("/")
     */
    public function index() {}

    /**
     * @Route\Post("/action")
     */
    public function action() {}

}