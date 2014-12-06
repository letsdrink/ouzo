<?php
namespace Ouzo;

abstract class Widget
{
    /**
     * @var View
     */
    protected $_view;

    abstract public function render();
}
