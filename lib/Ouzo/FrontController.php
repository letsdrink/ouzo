<?php
namespace Ouzo;

use Exception;
use Ouzo\Logger\Logger;
use Ouzo\Routing\Router;

class FrontController
{
    public static $requestId;
    public static $userId;

    private $_defaults;
    private $_currentAction;
    private $_currentController;
    private $_currentControllerObject;

    public $redirectHandler;
    public $sessionInitializer;
    public $downloadHandler;
    public $outputDisplayer;

    public function __construct()
    {
        self::$requestId = uniqid();

        $this->_defaults = Config::getValue('global');

        $this->redirectHandler = new RedirectHandler();
        $this->sessionInitializer = new SessionInitializer();
        $this->downloadHandler = new DownloadHandler();
        $this->controllerResolver = new ControllerResolver();
        $this->outputDisplayer = new OutputDisplayer();
    }

    public function init()
    {
        $uri = new Uri();

        $router = new Router($uri);
        $routeRule = $router->findRoute();

        $this->_currentController = $routeRule->getController();
        $this->_currentAction = $routeRule->isActionRequired() ? $routeRule->getAction() : $uri->getAction();

        if (!$routeRule->getController()) {
            $this->_redirectToDefault();
        } else {
            $this->_currentControllerObject = $this->controllerResolver->getController($this->_currentController, $this->_currentAction);

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
    }

    public function getCurrentControllerObject()
    {
        return $this->_currentControllerObject;
    }

    private function _redirectToDefault()
    {
        $this->_redirect('/' . $this->_defaults['controller'] . '/' . $this->_defaults['action']);
    }

    private function _redirect($url)
    {
        $prefixSystem = Config::getValue('global');
        $url = $prefixSystem['prefix_system'] . $url;
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
        if (method_exists($this->_currentControllerObject, $currentAction)) {
            $this->_currentControllerObject->$currentAction();
        } else {
            throw new FrontControllerException('No action [' . $currentAction . '] defined in controller [' . get_class($this->_currentControllerObject) . '].');
        }
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
        self::$userId = isset($_SESSION['id_user_ses']) ? $_SESSION['id_user_ses'] : null;
        Logger::getLogger(__CLASS__)->info('[Request:/%s/%s]', array($this->_currentController, $this->_currentAction));
    }

    private function _isRedirect()
    {
        return in_array($this->_currentControllerObject->getStatusResponse(), array('redirect', 'redirectOld'));
    }
}

class FrontControllerException extends \Exception
{
}