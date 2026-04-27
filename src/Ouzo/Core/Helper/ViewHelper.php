<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Helper {

    use Ouzo\AutoloadNamespaces;
    use Ouzo\Config;
    use Ouzo\ControllerUrl;
    use Ouzo\I18n;
    use Ouzo\PluralizeOption;
    use Ouzo\Session;
    use Ouzo\Utilities\Arrays;
    use Ouzo\Utilities\Date;
    use Ouzo\Utilities\Objects;
    use Ouzo\Utilities\Strings;
    use Ouzo\View;

    class ViewHelper
    {
        public static function url(array|string $params): string
        {
            return ControllerUrl::createUrl($params);
        }

        public static function renderWidget(string $widgetName, array $attributes = []): string
        {
            $className = ucfirst($widgetName);
            $viewWidget = new View($className . '/' . $widgetName, $attributes);

            $classLoad = AutoloadNamespaces::getWidgetNamespace() . $className;
            $widget = new $classLoad($viewWidget);

            return $widget->render();
        }

        public static function renderPartial(string $name, array $values = []): string
        {
            $view = new View($name, $values);
            return PartialTooltip::wrap($view->render(), $name);
        }

        public static function addFile(array $fileInfo = [], string $stringToRemove = ''): ?string
        {
            if (!empty($fileInfo)) {
                $prefixSystem = Config::getValue('global', 'prefix_system');
                $suffixCache = Config::getValue('global', 'suffix_cache');
                $suffixCache = !empty($suffixCache) ? '?' . $suffixCache : '';

                $url = $prefixSystem . $fileInfo['params']['url'] . $suffixCache;
                $url = Strings::remove($url, $stringToRemove);

                return ViewUtils::fileIncludeTag($fileInfo['type'], $url);
            }
            return null;
        }

        public static function addScript(string $url, string $stringToRemove = ''): ?string
        {
            return ViewHelper::addFile(['type' => 'script', 'params' => ['url' => $url]], $stringToRemove);
        }

        public static function addLink(string $url, string $stringToRemove = ''): ?string
        {
            return ViewHelper::addFile(['type' => 'link', 'params' => ['url' => $url]], $stringToRemove);
        }

        public static function showErrors(array $errors = []): ?string
        {
            if ($errors) {
                $errorView = new View('error_alert');
                $errorView->errors = $errors;
                return $errorView->render();
            }
            return null;
        }

        public static function showNotices(array $notices = []): ?string
        {
            if (Session::has('messages') || $notices) {
                $sessionMessages = Arrays::filterNotBlank(Arrays::toArray(Session::get('messages')));
                $notices = array_merge($sessionMessages, $notices);
                $noticeView = new View('notice_alert');
                $noticeView->notices = $notices;
                return $noticeView->render();
            }
            return null;
        }

        public static function showSuccess(array $notices = []): ?string
        {
            if (Session::has('messages') || $notices) {
                $sessionMessages = Arrays::filterNotBlank(Arrays::toArray(Session::get('messages')));
                $notices = array_merge($sessionMessages, $notices);
                $noticeView = new View('success_alert');
                $noticeView->notices = $notices;
                return $noticeView->render();
            }
            return null;
        }

        public static function showWarnings(array $warnings = []): ?string
        {
            if ($warnings) {
                $warningView = new View('warning_alert');
                $warningView->warnings = $warnings;
                return $warningView->render();
            }
            return null;
        }

        public static function formatDate(?string $date, string $format = 'Y-m-d'): ?string
        {
            return Date::formatDate($date, $format);
        }

        public static function formatDateTime(?string $date, string $format = 'Y-m-d H:i'): ?string
        {
            return Date::formatDateTime($date, $format);
        }

        public static function formatDateTimeWithSeconds(?string $date): ?string
        {
            return Date::formatDateTime($date, 'Y-m-d H:i:s');
        }

        public static function pluralise(int $count, array $words): ?string
        {
            return $words[$count == 1 ? 'singular' : 'plural'];
        }

        public static function t(string $textKey, array $params = [], ?PluralizeOption $pluralize = null): string|array
        {
            return I18n::t($textKey, $params, $pluralize);
        }

        public static function toString(mixed $object): string
        {
            return Objects::toString($object);
        }
    }
}

namespace {

    use Deprecated;
    use Ouzo\Helper\ViewHelper;
    use Ouzo\PluralizeOption;

    #[Deprecated(message: 'use ViewHelper::url() instead')]
    function url(array|string $params): string
    {
        return ViewHelper::url($params);
    }

    #[Deprecated(message: 'use ViewHelper::renderWidget() instead')]
    function renderWidget(string $widgetName, array $attributes = []): string
    {
        return ViewHelper::renderWidget($widgetName, $attributes);
    }

    #[Deprecated(message: 'use ViewHelper::renderPartial() instead')]
    function renderPartial(string $name, array $values = []): string
    {
        return ViewHelper::renderPartial($name, $values);
    }

    #[Deprecated(message: 'use ViewHelper::addFile() instead')]
    function addFile(array $fileInfo = [], string $stringToRemove = ''): ?string
    {
        return ViewHelper::addFile($fileInfo, $stringToRemove);
    }

    #[Deprecated(message: 'use ViewHelper::addScript() instead')]
    function addScript(string $url, string $stringToRemove = ''): ?string
    {
        return ViewHelper::addScript($url, $stringToRemove);
    }

    #[Deprecated(message: 'use ViewHelper::addLink() instead')]
    function addLink(string $url, string $stringToRemove = ''): ?string
    {
        return ViewHelper::addLink($url, $stringToRemove);
    }

    #[Deprecated(message: 'use ViewHelper::showErrors() instead')]
    function showErrors(array $errors = []): ?string
    {
        return ViewHelper::showErrors($errors);
    }

    #[Deprecated(message: 'use ViewHelper::showNotices() instead')]
    function showNotices(array $notices = []): ?string
    {
        return ViewHelper::showNotices($notices);
    }

    #[Deprecated(message: 'use ViewHelper::showSuccess() instead')]
    function showSuccess(array $notices = []): ?string
    {
        return ViewHelper::showSuccess($notices);
    }

    #[Deprecated(message: 'use ViewHelper::showWarnings() instead')]
    function showWarnings(array $warnings = []): ?string
    {
        return ViewHelper::showWarnings($warnings);
    }

    #[Deprecated(message: 'use ViewHelper::formatDate() instead')]
    function formatDate(?string $date, string $format = 'Y-m-d'): ?string
    {
        return ViewHelper::formatDate($date, $format);
    }

    #[Deprecated(message: 'use ViewHelper::formatDateTime() instead')]
    function formatDateTime(?string $date, string $format = 'Y-m-d H:i'): ?string
    {
        return ViewHelper::formatDateTime($date, $format);
    }

    #[Deprecated(message: 'use ViewHelper::formatDateTimeWithSeconds() instead')]
    function formatDateTimeWithSeconds(?string $date): ?string
    {
        return ViewHelper::formatDateTimeWithSeconds($date);
    }

    #[Deprecated(message: 'use ViewHelper::pluralise() instead')]
    function pluralise(int $count, array $words): ?string
    {
        return ViewHelper::pluralise($count, $words);
    }

    #[Deprecated(message: 'use ViewHelper::t() instead')]
    function t(string $textKey, array $params = [], ?PluralizeOption $pluralize = null): string|array
    {
        return ViewHelper::t($textKey, $params, $pluralize);
    }

    #[Deprecated(message: 'use ViewHelper::toString() instead')]
    function toString(mixed $object): string
    {
        return ViewHelper::toString($object);
    }
}