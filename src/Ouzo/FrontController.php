<?php
namespace Ouzo;

use Ouzo\Logger\Logger;
use Ouzo\Request\RequestContext;
use Ouzo\Routing\Router;
use Ouzo\Utilities\Strings;

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

    public function __construct()
    {
        self::$requestId = uniqid();

        $this->_defaults = Config::getValue('global');

        $this->redirectHandler = new RedirectHandler();
        $this->sessionInitializer = new SessionInitializer();
        $this->downloadHandler = new DownloadHandler();
        $this->controllerFactory = new ControllerFactory();
        $this->outputDisplayer = new OutputDisplayer();
        $this->httpAuthBasicHandler = new HttpAuthBasicHandler();
    }

    public function init()
    {
        $uri = new Uri();
        $router = new Router($uri);
        $routeRule = $router->findRoute();

        $this->_currentController = $routeRule->getController();
        $this->_currentAction = $routeRule->isActionRequired() ? $routeRule->getAction() : $uri->getAction();

        RequestContext::setCurrentController($this->_currentController);

        $this->_currentControllerObject = $this->controllerFactory->createController($routeRule);

        $this->sessionInitializer->startSession();

        $this->_logRequest();

        $this->_startOutputBuffer();

        $this->_invokeInit();
        if ($this->_invokeBeforeMethods()) {
            $this->_invokeAction();
            $this->_invokeAfterMethods();
        }

        $this->_doActionOnResponse();
    }

    public function getCurrentControllerObject()
    {
        return $this->_currentControllerObject;
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
        foreach ($this->_currentControllerObject->before as $method) {
            if (!$this->_currentControllerObject->$method()) {
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
        foreach ($this->_currentControllerObject->after as $method) {
            $this->_currentControllerObject->$method();
        }
    }

    private function _invokeAction()
    {
        $currentAction = $this->_currentControllerObject->currentAction;
        $this->_currentControllerObject->$currentAction();
    }

    private function _doActionOnResponse()
    {
        $controller = $this->_currentControllerObject;
        switch ($controller->getStatusResponse()) {
            case 'show':
                $controller->display();
                $this->_showOutputBuffer();
                break;
            case 'redirect':
                $this->_redirect($controller->getRedirectLocation());
                break;
            case 'redirectOld':
                $this->redirectHandler->redirect($controller->getRedirectLocation());
                break;
            case 'file':
                $this->downloadHandler->downloadFile($controller->getFileData());
                break;
            case 'stream':
                $this->downloadHandler->streamMediaFile($controller->getFileData());
                break;
            case 'httpAuthBasic':
                $credentials = $controller->getAuthCredentials();
                $this->httpAuthBasicHandler->authenticate($credentials['login'], $credentials['password']);
                $controller->display();
                $this->_showOutputBuffer();
                break;
        }
    }

    private function _startOutputBuffer()
    {
        ob_start();
    }

    private function _showOutputBuffer()
    {
        $page = ob_get_contents();
        ob_end_clean();
        $this->outputDisplayer->display($page);
    }

    private function _logRequest()
    {
        Logger::getLogger(__CLASS__)->info('[Request:/%s/%s]', array($this->_currentController, $this->_currentAction));
    }

    private function _isRedirect()
    {
        return in_array($this->_currentControllerObject->getStatusResponse(), array('redirect', 'redirectOld'));
    }

}