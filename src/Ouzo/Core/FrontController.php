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

    private $defaults;
    private $currentAction;
    private $currentController;
    private $currentControllerObject;

    /**
     * @Inject
     * @var \Ouzo\RedirectHandler
     */
    private $redirectHandler;
    /**
     * @Inject
     * @var \Ouzo\SessionInitializer
     */
    private $sessionInitializer;
    /**
     * @Inject
     * @var \Ouzo\DownloadHandler
     */
    private $downloadHandler;
    /**
     * @Inject
     * @var \Ouzo\OutputDisplayer
     */
    private $outputDisplayer;
    /**
     * @Inject
     * @var \Ouzo\HeaderSender
     */
    private $headerSender;
    /**
     * @Inject
     * @var \Ouzo\CookiesSetter
     */
    private $cookiesSetter;
    /**
     * @Inject
     * @var \Ouzo\ControllerFactory
     */
    private $controllerFactory;
    /**
     * @Inject
     * @var \Ouzo\Request\RequestContext
     */
    private $requestContext;

    public function __construct()
    {
        self::$requestId = uniqid();

        $this->defaults = Config::getValue('global');
    }

    public function init()
    {
        $uri = new Uri();
        $router = new Router($uri);
        $routeRule = $router->findRoute();

        $this->currentController = $routeRule->getController();
        $this->requestContext->setCurrentController($this->currentController);
        $this->currentAction = $routeRule->isActionRequired() ? $routeRule->getAction() : $uri->getAction();

        $this->sessionInitializer->startSession();

        $this->currentControllerObject = $this->controllerFactory->createController($routeRule);
        $this->requestContext->setCurrentControllerObject($this->currentControllerObject);

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
        if (method_exists($this->currentControllerObject, 'init')) {
            $this->currentControllerObject->init();
        }
    }

    private function _invokeBeforeMethods()
    {
        foreach ($this->currentControllerObject->before as $callback) {
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
        foreach ($this->currentControllerObject->after as $callback) {
            $this->callCallback($callback);
        }
    }

    private function _invokeAction()
    {
        $controller = $this->currentControllerObject;
        $currentAction = $controller->currentAction;
        call_user_func_array(array($controller, $currentAction), $controller->params);
        $this->_logRequestIfDebugEnabled();
    }

    private function _logRequestIfDebugEnabled()
    {
        if (Config::getValue('debug')) {
            Stats::traceHttpRequest($this->currentControllerObject->params);
        }
    }

    private function _doActionOnResponse()
    {
        $controller = $this->currentControllerObject;
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
        Logger::getLogger(__CLASS__)->info('[Request:/%s/%s]', array($this->currentController, $this->currentAction));
    }

    private function _isRedirect()
    {
        return in_array($this->currentControllerObject->getStatusResponse(), array('redirect', 'redirectOld'));
    }

    private function callCallback($callback)
    {
        if (is_string($callback)) {
            $callback = array($this->currentControllerObject, $callback);
        }
        return call_user_func($callback, $this->currentControllerObject);
    }

    private function renderOutput()
    {
        ob_start();
        $this->currentControllerObject->display();
        $page = ob_get_contents();
        ob_end_clean();
        $this->outputDisplayer->display($page);
    }

    /**
     * @return RedirectHandler
     */
    public function getRedirectHandler()
    {
        return $this->redirectHandler;
    }

    /**
     * @return SessionInitializer
     */
    public function getSessionInitializer()
    {
        return $this->sessionInitializer;
    }

    /**
     * @return DownloadHandler
     */
    public function getDownloadHandler()
    {
        return $this->downloadHandler;
    }

    /**
     * @return OutputDisplayer
     */
    public function getOutputDisplayer()
    {
        return $this->outputDisplayer;
    }

    /**
     * @return HeaderSender
     */
    public function getHeaderSender()
    {
        return $this->headerSender;
    }

    /**
     * @return CookiesSetter
     */
    public function getCookiesSetter()
    {
        return $this->cookiesSetter;
    }

    /**
     * @return ControllerFactory
     */
    public function getControllerFactory()
    {
        return $this->controllerFactory;
    }

    /**
     * @return RequestContext
     */
    public function getRequestContext()
    {
        return $this->requestContext;
    }
}
