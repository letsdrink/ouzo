<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Exception;
use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\ClassName;
use Ouzo\Utilities\Strings;

class Controller
{
    public $view;
    public $layout;
    public $before = array();
    public $after = array();
    public $currentController = '';
    public $currentAction = '';
    public $params;

    private $_statusResponse = 'show';
    private $_redirectLocation = '';
    private $_fileData = array();
    private $_headers = array();
    private $_cookies = array();
    private $_routeRule = null;
    private $_keepMessage = false;

    public function __construct(RouteRule $routeRule)
    {
        $this->_routeRule = $routeRule;
        $uri = new Uri();
        $this->currentController = $routeRule->getController();
        $this->currentAction = $routeRule->isActionRequired() ? $routeRule->getAction() : $uri->getAction();

        $viewName = $this->getViewName();

        $this->view = new View($viewName);
        $this->layout = new Layout();
        $this->params = $this->createParameters($routeRule, $uri);
    }

    public function header($header)
    {
        $this->_headers[] = $header;
    }

    public function setCookie($params)
    {
        $this->_cookies[] = $params;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    public function getNewCookies()
    {
        return $this->_cookies;
    }

    public function redirect($url, $messages = array())
    {
        $url = trim($url);
        $this->notice($messages, false, $url);

        $this->_redirectLocation = $url;
        $this->_statusResponse = 'redirect';
    }

    public function downloadFile($label, $mime, $path, $type = 'file')
    {
        $this->_fileData = array('label' => $label, 'mime' => $mime, 'path' => $path);
        $this->_statusResponse = $type;
    }

    public function setRedirectLocation($redirectLocation)
    {
        $this->_redirectLocation = $redirectLocation;
    }

    public function setStatusResponse($statusResponse)
    {
        $this->_statusResponse = $statusResponse;
    }

    public function getStatusResponse()
    {
        return $this->_statusResponse;
    }

    public function getRedirectLocation()
    {
        return $this->_redirectLocation;
    }

    public function getFileData()
    {
        return $this->_fileData;
    }

    public function display()
    {
        $renderedView = $this->view->getRenderedView();
        if ($renderedView) {
            $this->layout->setRenderContent($renderedView);
        }

        $this->layout->renderLayout();
        $this->_removeMessages();
    }

    private function _removeMessages()
    {
        if (!$this->_keepMessage && Session::has('messages')) {
            $messages = Arrays::filter(Session::get('messages'), function (Notice $notice) {
                return !$notice->requestUrlMatches();
            });
            $this->saveMessagesWithEmptyCheck($messages);
        }
    }

    public function renderAjaxView($viewName = null)
    {
        $view = $this->view->render($viewName ?: $this->getViewName());
        $this->layout->renderAjax($view);
    }

    public function notice($messages, $keep = false, $url = null)
    {
        if (!empty($messages)) {
            $url = $url ? Uri::addPrefixIfNeeded($url) : null;
            $messages = $this->wrapAsNotices($messages, $url);
            Session::set('messages', $messages);
            $this->_keepMessage = $keep;
        }
    }

    public static function getTab()
    {
        $noController = str_replace('Controller', '', get_called_class());
        $noSlashes = str_replace('\\', '', $noController);
        return Strings::camelCaseToUnderscore($noSlashes);
    }

    public function isAjax()
    {
        return Uri::isAjax();
    }

    public function getRouteRule()
    {
        return $this->_routeRule;
    }

    public function __call($name, $args)
    {
        throw new NoControllerActionException('No action [' . $name . '] defined in controller [' . get_called_class() . '].');
    }

    private function wrapAsNotices($messages, $url)
    {
        $array = Arrays::toArray($messages);
        return array_map(function ($msg) use ($url) {
            return new Notice($msg, $url);
        }, $array);
    }

    private function saveMessagesWithEmptyCheck($messages)
    {
        if (!$messages) {
            Session::remove('messages');
        } else {
            Session::set('messages', $messages);
        }
    }

    private function getViewName()
    {
        return (ClassName::pathToFullyQualifiedName($this->currentController) . '/' . $this->currentAction) ?: '/';
    }

    private function createParameters(RouteRule $routeRule, Uri $uri)
    {
        $parameters = $routeRule->getParameters() ? $routeRule->getParameters() : $uri->getParams();
        $requestParameters = Uri::getRequestParameters();
        return array_merge($parameters, $_POST, $_GET, $requestParameters);
    }
}

class NoControllerActionException extends Exception
{
}
