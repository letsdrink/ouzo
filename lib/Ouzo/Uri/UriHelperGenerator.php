<?php
namespace Ouzo\Uri;

use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class UriHelperGenerator
{
    /**
     * RouteRule[]
     */
    private $_routes;
    private $_generatedFunctions = '';

    /**
     * @return UriHelperGenerator
     */
    public static function generate()
    {
        return new self(Route::getRoutes());
    }

    function __construct($routes)
    {
        $this->_routes = $routes;
        $this->_generateFunctions();
    }

    private function _generateFunctions()
    {
        foreach ($this->_routes as $route) {
            $this->_generatedFunctions .= $this->_createFunction($route);
        }
    }

    private function _createFunction(RouteRule $routeRule)
    {
        $name = lcfirst(Strings::underscoreToCamelCase($routeRule->getName()));
        $uri = $routeRule->getUri();
        $parameters = $this->_prepareParameters($uri);

        $uriWithVariables = str_replace(':', '$', $uri);
        $parametersString = implode(', ', $parameters);

        $function = <<<FUNCTION
function $name($parametersString)
{
    return url(array('string' => '$uriWithVariables'));
}\n\n
FUNCTION;
        return $name ? $function : '';
    }

    private function _prepareParameters($uri)
    {
        preg_match_all('#:(\w+)#', $uri, $matches);
        $parameters = Arrays::getValue($matches, 1, array());
        return Arrays::map($parameters, function ($parameter) {
            return '$' . $parameter;
        });
    }

    public function getGeneratedFunctions()
    {
        return trim($this->_generatedFunctions);
    }
}