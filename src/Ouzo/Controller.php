<?php
namespace Ouzo;

use Ouzo\Routing\RouteRule;
use Ouzo\Utilities\Arrays;
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
    private $_routeRule = null;

    public function __construct(RouteRule $routeRule)
    {
        $this->_routeRule = $routeRule;
        $uri = new Uri();
        $this->currentController = $routeRule->getController();
        $this->currentAction = $routeRule->isActionRequired() ? $routeRule->getAction() : $uri->getAction();

        $viewName = Strings::underscoreToCamelCase($this->currentController) . DIRECTORY_SEPARATOR . $this->currentAction;

        $this->view = new View($viewName);
        $this->layout = new Layout();
        $requestParameters = Uri::getRequestParameters();
        $parameters = $routeRule->getParameters() ? $routeRule->getParameters() : $uri->getParams();
        $this->params = array_merge($_POST, $_GET, $requestParameters, $parameters);
    }

    public function redirect($url, $messages = array())
    {
        $this->notice($messages);

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
        unset($_SESSION['messages']);
    }

    public function renderAjaxView($viewName)
    {
        $view = $this->view->render($viewName);
        $this->layout->renderAjax($view);
    }

    public function notice($messages)
    {
        if (!empty($messages)) {
            $_SESSION['messages'] = Arrays::toArray($messages);
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
}