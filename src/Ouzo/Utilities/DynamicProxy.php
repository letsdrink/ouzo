<?php

namespace Ouzo\Utilities;

use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionParameter;

class DynamicProxy
{
    public static function newInstance($className, $methodHandler)
    {
        $name = 'DynamicProxy_' . str_replace('\\', '_', $className) . '_' . uniqid();
        eval(self::getProxyClassDefinition($name, $className));
        $object = null;
        eval("\$object = new $name(\$methodHandler);");
        $object->_methodHandler = $methodHandler;
        return $object;
    }

    private static function getProxyClassDefinition($name, $className) {
        $code = "class {$name} extends $className { public \$_methodHandler; ";
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
        return $methods;
    }

    private static function getParameterDeclaration(ReflectionFunctionAbstract $method)
    {
        return Joiner::on(', ')->join(Arrays::map($method->getParameters(), function(ReflectionParameter $param) {
            return '$' . $param->name;
        }));
    }

    public static function extractMethodHandler($proxy)
    {
        return $proxy->_methodHandler;
    }
} 