<?php
namespace Ouzo;

abstract class Widget
{
    protected $_view;

    abstract public function render();
}