<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Exception;
use Ouzo\Db\Stats;
use Ouzo\Logger\Logger;
use Ouzo\Request\RequestContext;
use Ouzo\Routing\Router;
use Ouzo\Utilities\Functions;

class FrontController
{
    public static $requestId;

    private $_defaults;
    private $_currentAction;
    private $_currentController;
    private $_currentControllerObject;

    public $redirectHandler;
    public $sessionInitializer;
    public $downloadHandler;
    public $outputDisplayer;
    public $httpAuthBasicHandler;
    public $headerSender;
    public $cookiesSetter;

    public function __construct()
    {
        self::$requestId = uniqid();

        $this->_defaults = Config::getValue('global');

        $this->redirectHandler = new RedirectHandler();
        $this->sessionInitializer = new SessionInitializer();
        $this->downloadHandler = new DownloadHandler();
        $this->controllerFactory = new ControllerFactory();
        $this->outputDisplayer = new OutputDisplayer();
        $this->headerSender = new HeaderSender();
        $this->cookiesSetter = new CookiesSetter();
    }

    public function init()
    {
        $uri = new Uri();
        $router = new Router($uri);
        $routeRule = $router->findRoute();

        $this->_currentController = $routeRule->getController();
        $this->_currentAction = $routeRule->isActionRequired() ? $routeRule->getAction() : $uri->getAction();

        RequestContext::setCurrentController($this->_currentController);

        $this->sessionInitializer->startSession();

        $this->_currentControllerObject = $this->controllerFactory->createController($routeRule);
        RequestContext::setCurrentControllerObject($this->_currentControllerObject);

        $this->_logRequest();

        $afterInitCallback = Config::getValue('callback', 'afterControllerInit');
        if ($afterInitCallback) {
            Functions::call($afterInitCallback, array());
        }

        try {
            ob_start();
            $this->_invokeControllerMethods();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        ob_end_flush();
    }

    public function _invokeControllerMethods()
    {
        $this->_invokeInit();
        if ($this->_invokeBeforeMethods()) {
            $this->_invokeAction();
            $this->_invokeAfterMethods();
        }

        $this->_doActionOnResponse();
    }

    private function _redirect($url)
    {
        $url = Uri::addPrefixIfNeeded($url);
        $this->redirectHandler->redirect($url);
    }

    private function _invokeInit()
    {
        if (method_exists($this->_currentControllerObject, 'init')) {
            $this->_currentControllerObject->init();
        }
    }

    private function _invokeBeforeMethods()
    {
        foreach ($this->_currentControllerObject->before as $callback) {
            if (!($this->callCallback($callback))) {
                return false;
            }
            if ($this->_isRedirect()) {
                return false;
            }
        }
        return true;
    }

    private function _invokeAfterMethods()
    {
        foreach ($this->_currentControllerObject->after as $callback) {
            $this->callCallback($callback);
        }
    }

    private function _invokeAction()
    {
        $currentAction = $this->_currentControllerObject->currentAction;
        $this->_currentControllerObject->$currentAction();
        $this->_logRequestIfDebugEnabled();
    }

    private function _logRequestIfDebugEnabled()
    {
        if (Config::getValue('debug')) {
            Stats::traceHttpRequest($this->_currentControllerObject->params);
        }
    }

    private function _doActionOnResponse()
    {
        $controller = $this->_currentControllerObject;
        $this->_sendHeaders($controller->getHeaders());
        $this->cookiesSetter->setCookies($controller->getNewCookies());
        switch ($controller->getStatusResponse()) {
            case 'show':
                $this->renderOutput();
                break;
            case 'redirect':
                $this->_redirect($controller->getRedirectLocation());
                break;
            case 'redirectOld':
                $this->redirectHandler->redirect($controller->getRedirectLocation());
                break;
            case 'file':
                session_write_close();
                $this->downloadHandler->downloadFile($controller->getFileData());
                break;
            case 'stream':
                session_write_close();
                $this->downloadHandler->streamMediaFile($controller->getFileData());
                break;
        }
    }

    private function _sendHeaders($headers)
    {
        $this->headerSender->send($headers);
    }

    private function _logRequest()
    {
        Logger::getLogger(__CLASS__)->info('[Request:/%s/%s]', array($this->_currentController, $this->_currentAction));
    }

    private function _isRedirect()
    {
        return in_array($this->_currentControllerObject->getStatusResponse(), array('redirect', 'redirectOld'));
    }

    private function callCallback($callback)
    {
        if (is_string($callback)) {
            $callback = array($this->_currentControllerObject, $callback);
        }
        return call_user_func($callback, $this->_currentControllerObject);
    }

    private function renderOutput()
    {
        ob_start();
        $this->_currentControllerObject->display();
        $page = ob_get_contents();
        ob_end_clean();
        $this->outputDisplayer->display($page);
    }
}
