<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Exception;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public function action()
    {
        echo "OUTPUT";
        $this->header('Location : http://foo.com');
    }

    public function redirect_to()
    {
        $this->redirect('/sample/add');
    }

    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }

    public function save()
    {
        $this->layout->renderAjax('save');
        $this->layout->unsetLayout();
    }

    public function except()
    {
        $this->layout->renderAjax('except');
        $this->layout->unsetLayout();
    }
}

class RestfulController extends Controller
{
    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }

    public function fresh()
    {
        $this->layout->renderAjax('fresh');
        $this->layout->unsetLayout();
    }

    public function create()
    {
        $this->layout->renderAjax('create');
        $this->layout->unsetLayout();
    }

    public function show()
    {
        $this->layout->renderAjax('show=' . $this->params['id']);
        $this->layout->unsetLayout();
    }

    public function edit()
    {
        $this->layout->renderAjax('edit=' . $this->params['id']);
        $this->layout->unsetLayout();
    }

    public function update()
    {
        $this->layout->renderAjax('update=' . $this->params['id']);
        $this->layout->unsetLayout();
    }

    public function destroy()
    {
        $this->layout->renderAjax('destroy=' . $this->params['id']);
        $this->layout->unsetLayout();
    }
}

namespace Ouzo\Api;

use Ouzo\Controller;

class SomeController extends Controller
{
    public function action()
    {
        $this->layout->renderAjax('some controller - action');
        $this->layout->unsetLayout();
    }
}
