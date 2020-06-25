<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Request\RequestHeaders;
use Ouzo\Request\RequestParameters;
use Ouzo\Routing\RouteRule;
use Ouzo\Stats\SessionStats;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Controller
{
    /** @var string */
    public static $stream = 'php://input';

    /** @var View */
    public $view;
    /** @var Layout */
    public $layout;
    /** @var array */
    public $before = [];
    /** @var array */
    public $after = [];
    /** @var string */
    public $currentController = '';
    /** @var string */
    public $currentAction = '';
    /** @var array */
    public $params;

    /** @var string */
    private $statusResponse = 'show';
    /** @var string */
    private $redirectLocation = '';
    /** @var array */
    private $fileData = [];
    /** @var array */
    private $headers = [];
    /** @var array */
    private $cookies = [];
    /** @var RouteRule|null */
    private $routeRule = null;
    /** @var Uri */
    private $uri;
    /** @var bool */
    private $keepMessage = false;
    /** @var SessionStats */
    private $sessionStats;

    /**
     * @param RouteRule $routeRule
     * @param RequestParameters $requestParameters
     * @param SessionStats $sessionStats
     * @return Controller
     */
    public static function createInstance(RouteRule $routeRule, RequestParameters $requestParameters, SessionStats $sessionStats)
    {
        $className = get_called_class();
        /** @var $controller Controller */
        $controller = new $className();
        $controller->initialize($routeRule, $requestParameters, $sessionStats);
        return $controller;
    }

    /**
     * @param RouteRule $routeRule
     * @param RequestParameters $requestParameters
     * @param SessionStats $sessionStats
     * @return void
     */
    public function initialize(RouteRule $routeRule, RequestParameters $requestParameters, SessionStats $sessionStats)
    {
        $this->routeRule = $routeRule;
        $this->sessionStats = $sessionStats;
        $this->uri = $requestParameters->getRoutingService()->getUri();
        $this->currentController = $routeRule->getController();
        $this->currentAction = $routeRule->isActionRequired() ? $routeRule->getAction() : $this->uri->getAction();

        $viewName = $this->getViewName();

        $this->view = new View($viewName);
        $this->layout = new Layout($this->view);
        $this->params = $requestParameters->get(static::$stream);
    }

    /**
     * @param string $header
     * @return void
     */
    public function header($header)
    {
        $this->headers[] = $header;
    }

    /**
     * @param string $params
     */
    public function setCookie($params)
    {
        $this->cookies[] = $params;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getNewCookies()
    {
        return $this->cookies;
    }

    /**
     * @param string $url
     * @param array $messages
     * @return void
     */
    public function redirect($url, $messages = [])
    {
        $url = trim($url);
        $this->notice($messages, false, $url);

        $this->redirectLocation = $url;
        $this->statusResponse = 'redirect';
    }

    /**
     * @param string $label
     * @param string $mime
     * @param string $path
     * @param string $type
     * @param string $data
     * @return void
     */
    public function downloadFile($label, $mime, $path, $type = 'file', $data = null)
    {
        $this->fileData = ['label' => $label, 'mime' => $mime, 'path' => $path, 'data' => $data];
        $this->statusResponse = $type;
    }

    /**
     * @param string $redirectLocation
     * @return void
     */
    public function setRedirectLocation($redirectLocation)
    {
        $this->redirectLocation = $redirectLocation;
    }

    /**
     * @param string $statusResponse
     * @return void
     */
    public function setStatusResponse($statusResponse)
    {
        $this->statusResponse = $statusResponse;
    }

    /**
     * @return string
     */
    public function getStatusResponse()
    {
        return $this->statusResponse;
    }

    /**
     * @return string
     */
    public function getRedirectLocation()
    {
        return $this->redirectLocation;
    }

    /**
     * @return array
     */
    public function getFileData()
    {
        return $this->fileData;
    }

    /**
     * @return void
     */
    public function display()
    {
        $renderedView = $this->view->getRenderedView();
        if ($renderedView) {
            $this->layout->setRenderContent($renderedView);
        }

        $this->layout->renderLayout();
        $this->_removeMessages();
    }

    /**
     * @return void
     */
    private function _removeMessages()
    {
        if (!$this->keepMessage && Session::isStarted() && Session::has('messages')) {
            $messages = Arrays::filter(Session::get('messages'), function (Notice $notice) {
                return !$notice->requestUrlMatches($this->uri);
            });
            $this->saveMessagesWithEmptyCheck($messages);
        }
    }

    /**
     * @param string|null $viewName
     */
    public function renderAjaxView($viewName = null)
    {
        $view = $this->view->render($viewName ?: $this->getViewName());
        $this->layout->renderAjax($view);
    }

    /**
     * @param array|string $messages
     * @param bool $keep
     * @param string|null $url
     */
    public function notice($messages, $keep = false, $url = null)
    {
        if (!empty($messages)) {
            $url = $url ? Uri::addPrefixIfNeeded($url) : null;
            $messages = $this->wrapAsNotices($messages, $url);
            Session::set('messages', $messages);
            $this->keepMessage = $keep;
        }
    }

    /**
     * @return string
     */
    public function getTab()
    {
        $noController = Strings::remove(get_called_class(), 'Controller');
        $noSlashes = Strings::remove($noController, '\\');
        return Strings::camelCaseToUnderscore($noSlashes);
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return Uri::isAjax();
    }

    /**
     * @return null|RouteRule
     */
    public function getRouteRule()
    {
        return $this->routeRule;
    }

    /**
     * @return SessionStats
     */
    public function getSessionStats()
    {
        return $this->sessionStats;
    }

    /**
     * @return Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    public function getCurrentControllerName()
    {
        return Strings::camelCaseToUnderscore($this->routeRule->getControllerName());
    }

    /**
     * @param string $name
     * @param array $args
     * @throws NoControllerActionException
     */
    public function __call($name, $args)
    {
        throw new NoControllerActionException('No action [' . $name . '] defined in controller [' . get_called_class() . '].');
    }

    /**
     * @param array|string $messages
     * @param string $url
     * @return array
     */
    private function wrapAsNotices($messages, $url)
    {
        $array = Arrays::toArray($messages);
        return Arrays::map($array, function ($msg) use ($url) {
            return new Notice($msg, $url);
        });
    }

    /**
     * @param string $messages
     * @return void
     */
    private function saveMessagesWithEmptyCheck($messages)
    {
        if ($messages) {
            Session::set('messages', $messages);
        } else {
            Session::remove('messages');
        }
    }

    /**
     * @return string
     */
    private function getViewName()
    {
        return ($this->routeRule->getControllerName() . '/' . $this->currentAction) ?: '/';
    }

    /**
     * @return array
     */
    public function getRequestHeaders()
    {
        return RequestHeaders::all();
    }
}
