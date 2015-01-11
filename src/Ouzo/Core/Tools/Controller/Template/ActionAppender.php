<?php
namespace Ouzo\Tools\Controller\Template;

use Ouzo\Utilities\Path;

class ActionAppender
{
    /**
     * @var ActionGenerator
     */
    private $actionGenerator;
    /**
     * @var ControllerGenerator
     */
    private $controllerGenerator = null;
    /**
     * @var ViewGenerator
     */
    private $viewGenerator = null;

    public function __construct(ActionGenerator $actionGenerator)
    {
        $this->actionGenerator = $actionGenerator;
    }

    public function toController(ControllerGenerator $controllerGenerator)
    {
        $this->controllerGenerator = $controllerGenerator;
        return $this;
    }

    public function toView(ViewGenerator $viewGenerator)
    {
        $this->viewGenerator = $viewGenerator;
        return $this;
    }

    public function append()
    {
        if ($this->controllerGenerator) {
            $controllerPath = $this->controllerGenerator->getControllerPath();
            $controllerContents = $this->controllerGenerator->getControllerContents();
            $actionContents = $this->actionGenerator->templateContents();
            $controllerContents = preg_replace('/}\\s$/', $actionContents . PHP_EOL . '}' . PHP_EOL, $controllerContents);
            file_put_contents($controllerPath, $controllerContents);
        }
        if ($this->viewGenerator) {
            file_put_contents(Path::join($this->viewGenerator->getViewPath(), $this->actionGenerator->getActionViewFile()), PHP_EOL);
        }
        return true;
    }
}
