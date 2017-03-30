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
    /** @var string */
    public static $requestId;

    private $defaults;
    /** @var string */
    private $currentAction;
    /** @var string */
    private $currentController;
    /** @var Controller */
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

    /**
     * @return void
     * @throws Exception
     */
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

        $this->logRequest();

        $afterInitCallback = Config::getValue('callback', 'afterControllerInit');
        if ($afterInitCallback) {
            Functions::call($afterInitCallback, []);
        }

        try {
            ob_start();
            $this->invokeControllerMethods();
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        ob_end_flush();
    }

    /**
     * @return void
     */
    public function invokeControllerMethods()
    {
        $this->invokeInit();
        if ($this->invokeBeforeMethods()) {
            $this->invokeAction();
            $this->invokeAfterMethods();
        }

        $this->doActionOnResponse();
    }

    /**
     * @param string $url
     * @return void
     */
    private function redirect($url)
    {
        $url = Uri::addPrefixIfNeeded($url);
        $this->redirectHandler->redirect($url);
    }

    /**
     * @return void
     */
    private function invokeInit()
    {
        if (method_exists($this->currentControllerObject, 'init')) {
            $this->currentControllerObject->init();
        }
    }

    /**
     * @return bool
     */
    private function invokeBeforeMethods()
    {
        foreach ($this->currentControllerObject->before as $callback) {
            if (!($this->callCallback($callback))) {
                return false;
            }
            if ($this->isRedirect()) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return void
     */
    private function invokeAfterMethods()
    {
        foreach ($this->currentControllerObject->after as $callback) {
            $this->callCallback($callback);
        }
    }

    /**
     * @return void
     */
    private function invokeAction()
    {
        $controller = $this->currentControllerObject;
        $currentAction = $controller->currentAction;
        call_user_func_array([$controller, $currentAction], $controller->getRouteRule()->getParameters());
        $this->logRequestIfDebugEnabled();
    }

    /**
     * @return void
     */
    private function logRequestIfDebugEnabled()
    {
        if (Config::getValue('debug')) {
            Stats::traceHttpRequest($this->currentControllerObject->params);
        }
    }

    /**
     * @return void
     */
    private function doActionOnResponse()
    {
        $controller = $this->currentControllerObject;
        $this->sendHeaders($controller->getHeaders());
        $this->cookiesSetter->setCookies($controller->getNewCookies());
        switch ($controller->getStatusResponse()) {
            case 'show':
                $this->renderOutput();
                break;
            case 'redirect':
                $this->redirect($controller->getRedirectLocation());
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

    /**
     * @param array $headers
     * @return void
     */
    private function sendHeaders($headers)
    {
        $this->headerSender->send($headers);
    }

    /**
     * @return void
     */
    private function logRequest()
    {
        Logger::getLogger(__CLASS__)->info('[Request:/%s/%s]', [$this->currentController, $this->currentAction]);
    }

    /**
     * @return bool
     */
    private function isRedirect()
    {
        return in_array($this->currentControllerObject->getStatusResponse(), ['redirect', 'redirectOld']);
    }

    /**
     * @param $callback
     * @return mixed
     */
    private function callCallback($callback)
    {
        if (is_string($callback)) {
            $callback = [$this->currentControllerObject, $callback];
        }
        return call_user_func($callback, $this->currentControllerObject);
    }

    /**
     * @return void
     */
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
