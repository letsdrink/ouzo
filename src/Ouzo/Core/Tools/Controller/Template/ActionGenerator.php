<?php
namespace Ouzo\Tools\Controller\Template;

class ActionGenerator
{
    private $action;

    public function __construct($action)
    {
        $this->action = $action;
    }

    public function getActionName()
    {
        return $this->action;
    }

    public function getActionViewFile()
    {
        return $this->action . '.phtml';
    }

    public function templateContents()
    {
        $classStubPlaceholderReplacer = new ActionStubPlaceholderReplacer($this);
        return $classStubPlaceholderReplacer->content();
    }
}
