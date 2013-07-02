<?php
namespace Thulium;

use Thulium\Utilities\Strings;

class Controller
{
    public $view;
    public $layout;
    public $before = array();
    public $after = array();
    public $currentController = '';
    public $currentAction = '';
    public $uri;
    public $params;

    private $_statusResponse = 'show';
    private $_redirectLocation = '';
    private $_fileData = array();

    public function __construct()
    {
        $uri = new \Thulium\Uri();

        $viewName = ucfirst($uri->getController()) . DIRECTORY_SEPARATOR . $uri->getAction();

        $this->uri = $uri;
        $this->currentController = $uri->getController();
        $this->currentAction = $uri->getAction();
        $this->view = new \Thulium\View($viewName);
        $this->layout = new \Thulium\Layout();
        $this->params = array_merge($_POST, $_GET, $this->uri->getParams());
    }

    public function redirect($url, $messages = array())
    {
        $this->notice($messages);

        $this->_redirectLocation = $url;
        $this->_statusResponse = 'redirect';
    }

    public function redirectOld($url, $params = Array())
    {
        $this->_statusResponse = 'redirectOld';
        $this->_redirectLocation = $url;
        if (!empty($params))
            $this->_redirectLocation .= '?' . http_build_query($params);
    }

    public function downloadFile($label, $mime, $path, $type = 'file')
    {
        $this->_fileData = array('label' => $label, 'mime' => $mime, 'path' => $path);
        $this->_statusResponse = $type;
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
            $_SESSION['messages'] = is_array($messages) ? $messages : array($messages);
        }
    }

    public static function getTab()
    {
        $noController = str_replace('Controller', '', get_called_class());
        $noSlashes = str_replace('\\', '', $noController);
        return Strings::camelCaseToUnderscore($noSlashes);
    }
}