<?php
namespace Thulium;

abstract class Widget
{
    protected $_view;

    abstract public function render();
}