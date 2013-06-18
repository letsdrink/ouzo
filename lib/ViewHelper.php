<?php
use Thulium\Config;
use Thulium\I18n;
use Thulium\Utilities\Date;

function url(array $params)
{
    $prefixSystem = Config::load()->getConfig('global');

    if (!empty($params['controller']) && !empty($params['action'])) {
        $returnUrl = $prefixSystem['prefix_system'];
        $returnUrl .= '/' . $params['controller'];
        $returnUrl .= '/' . $params['action'];

        if (!empty($params['extraParams'])) {
            $returnUrl .= _mergeParams($params['extraParams']);
        }
        return $returnUrl;
    }
    if (!empty($params['string'])) {
        return $prefixSystem['prefix_system'] . $params['string'];
    }
    throw new InvalidArgumentException('Illegal arguments');
}

function _mergeParams(array $params)
{
    $merged = '';
    foreach ($params as $param => $value) {
        $merged .= '/' . $param . '/' . $value;
    }
    return $merged;
}

function renderWidget($widgetName)
{
    $className = ucfirst($widgetName);
    $viewWidget = new \Thulium\View($className . '/' . $widgetName);

    $classLoad = '\Widget\\' . $className;
    $widget = new $classLoad($viewWidget);

    return $widget->render();
}

function renderPartial($name, array $values = array())
{
    $view = new \Thulium\View($name, $values);
    return $view->render();
}

function addFile(array $fileInfo = array(), $panel2_0 = true)
{
    if (!empty($fileInfo)) {

        $prefixSystem = Config::load()->getConfig('global');

        if(!$panel2_0)
            $prefixSystem['prefix_system'] = str_replace('/panel2.0','',$prefixSystem['prefix_system']);

        $url = $prefixSystem['prefix_system'] . $fileInfo['params']['url'];

        switch ($fileInfo['type']) {
            case 'link':
                return '<link rel="stylesheet" href="' . $url . '" type="text/css" />' . PHP_EOL;
            case 'script':
                return '<script type="text/javascript" src="' . $url . '"></script>' . PHP_EOL;
        }
    }

    return null;
}

function showErrors($errors)
{
    if ($errors) {
        $errorView = new \Thulium\View('error_alert');
        $errorView->errors = $errors;
        return $errorView->render();
    }
}

function showNotices()
{
    if (isset($_SESSION['messages'])) {
        $noticeView = new \Thulium\View('notice_alert');
        $noticeView->notices = $_SESSION['messages'];
        return $noticeView->render();
    }
}

function formatDate($date, $format = 'Y-m-d')
{
    return Date::formatDate($date, $format);
}

function formatDateTime($date, $format = 'Y-m-d H:i')
{
    return Date::formatDateTime($date, $format);
}

function formatDateTimeWithSeconds($date)
{
    return Date::formatDateTime($date, 'Y-m-d H:i:s');
}

function pluralise($count, $words)
{
    return $words[$count == 1 ? 'singular' : 'plural'];
}

function t($textKey)
{
    return I18n::t($textKey);
}

function toString($object)
{
    return \Thulium\Utilities\Objects::toString($object);
}