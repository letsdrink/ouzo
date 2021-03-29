<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Request\RequestHeaders;
use Ouzo\Request\RequestParameters;
use Ouzo\Routing\RouteRule;
use Ouzo\Stats\SessionStats;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Controller
{
    public static string $stream = 'php://input';

    public View $view;
    public Layout $layout;
    public array $before = [];
    public array $after = [];
    public string $currentController = '';
    public ?string $currentAction = '';
    public array $params;

    private string $statusResponse = 'show';
    private string $redirectLocation = '';
    private array $fileData = [];
    private array $headers = [];
    private array $cookies = [];
    private ?RouteRule $routeRule = null;
    private Uri $uri;
    private bool $keepMessage = false;
    private SessionStats $sessionStats;

    public static function createInstance(RouteRule $routeRule, RequestParameters $requestParameters, SessionStats $sessionStats): Controller
    {
        $className = get_called_class();
        /** @var $controller Controller */
        $controller = new $className();
        $controller->initialize($routeRule, $requestParameters, $sessionStats);
        return $controller;
    }

    public function initialize(RouteRule $routeRule, RequestParameters $requestParameters, SessionStats $sessionStats): void
    {
        $this->routeRule = $routeRule;
        $this->sessionStats = $sessionStats;
        $this->uri = $requestParameters->getRoutingService()->getUri();
        $this->currentController = $routeRule->getController();
        $this->currentAction = $routeRule->isRequiredAction() ? $routeRule->getAction() : $this->uri->getAction();

        $viewName = $this->getViewName();

        $this->view = new View($viewName);
        $this->layout = new Layout($this->view);
        $this->params = $requestParameters->get(static::$stream);
    }

    public function header(string $header): void
    {
        $this->headers[] = $header;
    }

    public function setCookie(string $params): void
    {
        $this->cookies[] = $params;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getNewCookies(): array
    {
        return $this->cookies;
    }

    public function redirect(string $url, string|array $messages = []): void
    {
        $url = trim($url);
        $this->notice($messages, false, $url);

        $this->redirectLocation = $url;
        $this->statusResponse = 'redirect';
    }

    public function downloadFile(string $label, string $mime, string $path, string $type = 'file', ?string $data = null): void
    {
        $this->fileData = ['label' => $label, 'mime' => $mime, 'path' => $path, 'data' => $data];
        $this->statusResponse = $type;
    }

    public function setRedirectLocation(string $redirectLocation): void
    {
        $this->redirectLocation = $redirectLocation;
    }

    public function setStatusResponse(string $statusResponse): void
    {
        $this->statusResponse = $statusResponse;
    }

    public function getStatusResponse(): string
    {
        return $this->statusResponse;
    }

    public function getRedirectLocation(): string
    {
        return $this->redirectLocation;
    }

    public function getFileData(): array
    {
        return $this->fileData;
    }

    public function display(): void
    {
        $renderedView = $this->view->getRenderedView();
        if ($renderedView) {
            $this->layout->setRenderContent($renderedView);
        }

        $this->layout->renderLayout();
        $this->removeMessages();
    }

    private function removeMessages(): void
    {
        if (!$this->keepMessage && Session::isStarted() && Session::has('messages')) {
            $messages = Arrays::filter(Session::get('messages'), fn(Notice $notice) => !$notice->requestUrlMatches($this->uri));
            $this->saveMessagesWithEmptyCheck($messages);
        }
    }

    public function renderAjaxView(?string $viewName = null): void
    {
        $view = $this->view->render($viewName ?: $this->getViewName());
        $this->layout->renderAjax($view);
    }

    public function notice(array|string $messages, bool $keep = false, ?string $url = null): void
    {
        if (!empty($messages)) {
            $url = $url ? Uri::addPrefixIfNeeded($url) : null;
            $messages = $this->wrapAsNotices(Arrays::toArray($messages), $url);
            Session::set('messages', $messages);
            $this->keepMessage = $keep;
        }
    }

    public function getTab(): string
    {
        $noController = Strings::remove(get_called_class(), 'Controller');
        $noSlashes = Strings::remove($noController, '\\');
        return Strings::camelCaseToUnderscore($noSlashes);
    }

    public function isAjax(): bool
    {
        return Uri::isAjax();
    }

    public function getRouteRule(): ?RouteRule
    {
        return $this->routeRule;
    }

    public function getSessionStats(): SessionStats
    {
        return $this->sessionStats;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function getCurrentControllerName(): string
    {
        return Strings::camelCaseToUnderscore($this->routeRule->getControllerName());
    }

    public function __call(string $name, array $args)
    {
        $class = get_called_class();
        throw new NoControllerActionException("No action [{$name}] defined in controller [{$class}].");
    }

    private function wrapAsNotices(array $messages, ?string $url): array
    {
        return Arrays::map($messages, fn($msg) => new Notice($msg, $url));
    }

    private function saveMessagesWithEmptyCheck(array $messages): void
    {
        if ($messages) {
            Session::set('messages', $messages);
        } else {
            Session::remove('messages');
        }
    }

    private function getViewName(): string
    {
        $controllerName = $this->routeRule->getControllerName();
        $action = $this->currentAction ?: '';
        return "{$controllerName}/{$action}";
    }

    public function getRequestHeaders(): array
    {
        return RequestHeaders::all();
    }
}
