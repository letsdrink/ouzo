<?php
namespace Controller;

use Ouzo\Controller;

class HelloWorldController extends Controller
{
    public function init()
    {
        $this->layout->setLayout('sample_layout');
    }

    public function index()
    {
        $this->view->render();
    }
}