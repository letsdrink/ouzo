<?php
namespace Ouzo\Tools\Model\Template;

use Ouzo\Utilities\Path;

class ClassPathResolver
{
    private $className;
    private $nameSpace;

    private function __construct($className, $nameSpace)
    {
        $this->className = $className;
        $this->nameSpace = $nameSpace;
    }

    public static function forClassAndNamespace($className, $nameSpace = null)
    {
        return new self($className, $nameSpace);
    }

    private function resolvePathFromNameSpace()
    {
        $parts = explode('\\', $this->nameSpace);
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    public function getClassFileName()
    {
        return Path::join(ROOT_PATH, $this->resolvePathFromNameSpace(), $this->className . ".php");
    }
}
