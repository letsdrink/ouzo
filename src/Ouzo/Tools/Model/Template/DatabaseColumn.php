<?php
namespace Ouzo\Tools\Model\Template;

class DatabaseColumn
{
    public $name;
    public $type;
    public $default;

    public function __construct($name, $type, $default = '')
    {
        $this->name = $name;
        $this->type = $type;
        $this->default = $default;
    }
}