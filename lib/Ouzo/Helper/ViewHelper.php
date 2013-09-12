<?php
use Ouzo\Config;
use Ouzo\ControllerUrl;
use Ouzo\I18n;
use Ouzo\Utilities\Date;

function url(array $params)
{
    return ControllerUrl::createUrl($params);
}

function renderWidget($widgetName)
{
    $className = ucfirst($widgetName);
    $viewWidget = new \Ouzo\View($className . '/' . $widgetName);

    $classLoad = '\Widget\\' . $className;
    $widget = new $classLoad($viewWidget);

    return $widget->render();
}

function renderPartial($name, array $values = array())
{
    $view = new \Ouzo\View($name, $values);
    return $view->render();
}

function addFile(array $fileInfo = array(), $panel2_0 = true)
{
    if (!empty($fileInfo)) {
        $defaults = Config::load()->getConfig('global');

        if (!$panel2_0) {
            $defaults['prefix_system'] = str_replace('/panel2.0', '', $defaults['prefix_system']);
        }

        $suffixCache = !empty($defaults['suffix_cache']) ? '?' . $defaults['suffix_cache'] : '';
        $url = $defaults['prefix_system'] . $fileInfo['params']['url'] . $suffixCache;

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
        $errorView = new \Ouzo\View('error_alert');
        $errorView->errors = $errors;
        return $errorView->render();
    }
}

function showNotices()
{
    if (isset($_SESSION['messages'])) {
        $noticeView = new \Ouzo\View('notice_alert');
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
    return \Ouzo\Utilities\Objects::toString($object);
}