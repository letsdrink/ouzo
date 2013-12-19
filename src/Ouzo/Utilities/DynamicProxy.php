<?php

namespace Ouzo\Utilities;

use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;

class DynamicProxy
{
    public static function newInstance($className, $methodHandler)
    {
        $name = 'DynamicProxy_' . str_replace('\\', '_', $className) . '_' . uniqid();
        eval(self::getProxyClassDefinition($name, $className));
        $object = null;
        eval("\$object = new $name(\$methodHandler);");
        return $object;
    }

    private static function getProxyClassDefinition($name, $className)
    {
        $code = "class {$name} extends $className { public \$_methodHandler;\n";
        $code .= "function __construct(\$methodHandler) { \$this->_methodHandler = \$methodHandler; }\n";
        foreach (self::getClassMethods($className) as $method) {
            $params = self::getParameterDeclaration($method);
            $code .= "function {$method->name}($params) { return call_user_func_array(array(\$this->_methodHandler, __FUNCTION__), func_get_args()); }\n";
        }
        $code .= '}';
        return $code;
    }

    private static function getClassMethods($className)
    {
        $class = new ReflectionClass($className);
        $methods = $class->getMethods();
        return Arrays::filter($methods, function(ReflectionMethod $method) {
            return !$method->isConstructor();
        });
    }

    private static function getParameterDeclaration(ReflectionFunctionAbstract $method)
    {
        return Joiner::on(', ')->join(Arrays::map($method->getParameters(), function (ReflectionParameter $param) {
            $result = '';
            if ($param->getClass()) {
                $result .= $param->getClass()->getName() . ' ';
            }
            if ($param->isArray()) {
                $result .= 'array ';
            }
            $result .= '$' . $param->name;
            if ($param->isDefaultValueAvailable()) {
                $result .= " = null"; // methodHandler gets only the passed arguments so anything would work here
            }
            return $result;
        }));
    }

    public static function extractMethodHandler($proxy)
    {
        return $proxy->_methodHandler;
    }
} 