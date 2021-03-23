<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;

use Exception;
use Ouzo\Controller;
use Ouzo\CookiesSetter;
use Ouzo\DownloadHandler;
use Ouzo\Exception\ValidationException;
use Ouzo\HeaderSender;
use Ouzo\Http\ResponseMapper;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\OutputDisplayer;
use Ouzo\RedirectHandler;
use Ouzo\Uri;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Objects;
use ReflectionClass;

class RequestExecutor
{
    #[Inject]
    public function __construct(
        private HeaderSender $headerSender,
        private CookiesSetter $cookiesSetter,
        private RedirectHandler $redirectHandler,
        private DownloadHandler $downloadHandler,
        private OutputDisplayer $outputDisplayer,
        private RequestParameterSerializer $requestParameterSerializer,
        private RequestParameterValidator $requestParameterValidator
    )
    {
    }

    public function execute(RequestContext $requestContext): void
    {
        $controller = $requestContext->getCurrentControllerObject();

        $this->invokeInit($controller);

        if ($this->invokeBeforeMethods($controller)) {
            $result = $this->invokeAction($controller);
            $this->setResponseCode($controller, $result);
            $this->serializeAndRenderJsonResponse($controller, $result);
            $this->invokeAfterMethods($controller);
        }

        $this->doActionOnResponse($controller);
    }

    public function getHeaderSender(): HeaderSender
    {
        return $this->headerSender;
    }

    public function getCookiesSetter(): CookiesSetter
    {
        return $this->cookiesSetter;
    }

    public function getRedirectHandler(): RedirectHandler
    {
        return $this->redirectHandler;
    }

    public function getDownloadHandler(): DownloadHandler
    {
        return $this->downloadHandler;
    }

    public function getOutputDisplayer(): OutputDisplayer
    {
        return $this->outputDisplayer;
    }

    private function invokeInit(Controller $controller): void
    {
        if (method_exists($controller, 'init')) {
            $controller->init();
        }
    }

    private function invokeBeforeMethods(Controller $controller): bool
    {
        foreach ($controller->before as $callback) {
            if (!$this->invokeCallback($callback, $controller)) {
                return false;
            }
            if ($this->isRedirect($controller)) {
                return false;
            }
        }
        return true;
    }

    private function invokeAction(Controller $controller): mixed
    {
        $currentAction = $controller->currentAction;

        $parameters = $this->getParameters($controller, $currentAction);
        $controllerClass = new ReflectionClass($controller);
        if ($controllerClass->hasMethod($currentAction)) {
            $numberOfParameters = $controllerClass->getMethod($currentAction)->getNumberOfParameters();
            if ($numberOfParameters > 0) {
                if ($numberOfParameters > sizeof($parameters)) {
                    throw new Exception("Invalid number of parameters. Expected: {$numberOfParameters}, but was: " . Objects::toString($parameters));
                }
                return call_user_func_array([$controller, $currentAction], array_values($parameters));
            }
        }
        return $controller->$currentAction();
    }

    private function invokeAfterMethods(Controller $controller): void
    {
        foreach ($controller->after as $callback) {
            $this->invokeCallback($callback, $controller);
        }
    }

    private function doActionOnResponse(Controller $controller): void
    {
        $this->headerSender->send($controller->getHeaders());
        $this->cookiesSetter->setCookies($controller->getNewCookies());

        switch ($controller->getStatusResponse()) {
            case 'show':
                $this->renderOutput($controller);
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

    private function invokeCallback(mixed $callback, Controller $controller): mixed
    {
        if (is_string($callback)) {
            $callback = [$controller, $callback];
        }

        return call_user_func($callback, $controller);
    }

    private function isRedirect(Controller $controller): bool
    {
        return in_array($controller->getStatusResponse(), ['redirect', 'redirectOld']);
    }

    private function renderOutput(Controller $controller): void
    {
        ob_start();
        $controller->display();
        $page = ob_get_contents();
        ob_end_clean();
        $this->outputDisplayer->display($page);
    }

    private function redirect(string $url): void
    {
        $this->redirectHandler->redirect(Uri::addPrefixIfNeeded($url));
    }

    private function getParameters(Controller $controller, string $currentAction): array
    {
        $deserializedParameters = $this->getActionParameters($controller, $currentAction);
        $this->validateRequestParameters($deserializedParameters);
        return array_merge($controller->getRouteRule()->getParameters(), $deserializedParameters);
    }

    private function getActionParameters(Controller $controller, string $currentAction): array
    {
        $class = new ReflectionClass($controller);
        if ($class->hasMethod($currentAction)) {
            return FluentArray::from($class->getMethod($currentAction)->getParameters())
                ->filter(fn($param) => $param->getType() && !$param->getType()->isBuiltin())
                ->map(fn($param) => $this->requestParameterSerializer->arrayToObject($controller->params, $param->getType()->getName()))
                ->toArray();
        }
        return [];
    }

    private function validateRequestParameters(array $params): void
    {
        $violations = [];
        foreach ($params as $param) {
            $violations = array_merge($violations, $this->requestParameterValidator->validate($param));
        }
        if ($violations) {
            throw new ValidationException($violations);
        }
    }

    private function serializeAndRenderJsonResponse(Controller $controller, $result): void
    {
        if (!is_null($result)) {
            $json = $this->requestParameterSerializer->objectToJson($result);
            $controller->layout->renderAjax($json);
        }
    }

    private function setResponseCode(Controller $controller, $result): void
    {
        $responseCode = Arrays::getValue($controller->getRouteRule()->getOptions(), 'code');
        if (!is_null($responseCode)) {
            $controller->header(ResponseMapper::getMessageWithHttpProtocol($responseCode));
        } else if (!is_null($result)) {
            $controller->header(ResponseMapper::getMessageWithHttpProtocol(200));
        }
    }
}
