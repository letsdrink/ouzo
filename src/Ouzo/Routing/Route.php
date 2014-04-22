<?php
namespace Ouzo\Routing;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

/**
 * Routes define URLs mapping to controllers and actions.
 *
 * Sample usage:
 * <code>
 *  Route::get('/agents/index', 'agents#index'); will match: GET /agents/index
 *  Route::post('/agents/save', 'agents#save'); will match: POST /agents/save
 *  Route::resource('agents'); will mapping RESTs methods (index, fresh, edit, show, create, update, destroy)
 *  Route::any('/agents/show_numbers', 'agents#show_numbers'); will match: POST or GET /agents/show_numbers
 *  Route::allowAll('/agents', 'agents'); will mapping any methods to all actions in controller
 * </code>
 *
 * To show all routes or routes per controller:
 * <code>
 *  Route::getRoutes();
 *  Route::getRoutesForController('agents');
 * </code>
 */
class Route
{
    /**
     * @var RouteRule[]
     */
    public static $routes = array();
    public static $methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');
    public static $validate = true;


    public static function get($uri, $action, array $options = array())
    {
        self::_addRoute('GET', $uri, $action, true, $options);
    }

    public static function post($uri, $action, array $options = array())
    {
        self::_addRoute('POST', $uri, $action, true, $options);
    }

    public static function put($uri, $action, array $options = array())
    {
        self::_addRoute('PUT', $uri, $action, true, $options);
    }

    public static function delete($uri, $action, array $options = array())
    {
        self::_addRoute('DELETE', $uri, $action, true, $options);
    }

    public static function any($uri, $action, array $options = array())
    {
        self::_addRoute(self::$methods, $uri, $action, true, $options);
    }

    public static function resource($controller)
    {
        self::_addResourceRoute($controller, 'GET', '', 'index');
        self::_addResourceRoute($controller, 'GET', '/fresh', 'fresh');
        self::_addResourceRoute($controller, 'GET', '/:id/edit', 'edit');
        self::_addResourceRoute($controller, 'GET', '/:id', 'show');
        self::_addResourceRoute($controller, 'POST', '', 'create');
        self::_addResourceRoute($controller, 'PUT', '/:id', 'update');
        self::_addResourceRoute($controller, 'PATCH', '/:id', 'update');
        self::_addResourceRoute($controller, 'DELETE', '/:id', 'destroy');
    }

    public static function allowAll($uri, $controller, $options = array())
    {
        self::_addRoute(self::$methods, $uri, $controller, false, $options);
    }

    private static function _createRouteUri($action, $suffix = '')
    {
        return '/' . $action . $suffix;
    }

    private static function _createRouteAction($controller, $action)
    {
        return $controller . '#' . $action;
    }

    private static function _addRoute($method, $uri, $action, $requireAction = true, $options = array(), $isResource = false)
    {
        if (self::$validate && self::_existRouteRule($method, $uri)) {
            $methods = is_array($method) ? implode(', ', $method) : $method;
            throw new InvalidArgumentException('Route rule for method ' . $methods . ' and URI "' . $uri . '" already exists');
        }

        $routeRule = new RouteRule($method, $uri, $action, $requireAction, $options, $isResource);
        if ($routeRule->hasRequiredAction()) {
            throw new InvalidArgumentException('Route rule ' . $uri . ' required action');
        }
        self::$routes[] = $routeRule;
    }

    private static function _existRouteRule($method, $uri)
    {
        return Arrays::any(self::getRoutes(), function (RouteRule $routeRule) use ($method, $uri) {
            return $routeRule->getMethod() == $method && $routeRule->getUri() == $uri;
        });
    }

    /**
     * @return RouteRule[]
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function getRoutesForController($controller)
    {
        return Arrays::filter(self::getRoutes(), function (RouteRule $route) use ($controller) {
            return Strings::equalsIgnoreCase($route->getController(), $controller);
        });
    }

    private static function _addResourceRoute($controller, $method, $uriSuffix, $action)
    {
        self::_addRoute($method,
            self::_createRouteUri($controller, $uriSuffix),
            self::_createRouteAction($controller, $action),
            true, array(), true
        );
    }
}