<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Uri\PathProviderInterface;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Strings;

class Uri
{
    #[Inject]
    public function __construct(private PathProviderInterface $pathProvider)
    {
    }

    public function getParams(): array
    {
        $path = $this->pathProvider->getPath();
        $pathElements = $this->parsePath($path, 3);
        return $this->splitParamsKeyValueMap($pathElements);
    }

    private function splitParamsKeyValueMap(array $pathElements): array
    {
        $paramsArray = [];
        if (!empty($pathElements[2])) {
            $params = $pathElements[2];
            $paramsGet = strpos($params, '&') ? str_replace('?', '', (strstr($params, '?') ?: $params)) : '';
            $paramsUrl = strstr($params, '?', true) ?: $params;
            parse_str($paramsGet, $parsedParamsGet);
            $paramsArray = array_merge($paramsArray, $this->parseParams($paramsUrl), $parsedParamsGet);
        }
        return $paramsArray;
    }

    private function parseParams(string $params): array
    {
        $paramsArray = [];
        $paramsParts = explode('/', $params);
        $k = 0;
        for ($i = 0; $i < (int)floor((count($paramsParts) / 2)); $i++) {
            $tmpKey = $paramsParts[$k];
            $tmpValue = $paramsParts[$k + 1];
            if (!empty($tmpValue)) {
                $paramsArray[$tmpKey] = $tmpValue;
            }
            $k = $k + 2;
        }
        return $paramsArray;
    }

    public function getPath(): string
    {
        $parseUrl = parse_url($this->pathProvider->getPath(), PHP_URL_PATH) ?: '/';
        return $this->removeDuplicatedSlashes($parseUrl);
    }

    private function removeDuplicatedSlashes(string $parseUrl): string
    {
        return preg_replace('#/{2,}#', '/', $parseUrl);
    }

    public function getPathWithoutPrefix(): string
    {
        $prefix = Config::getValue('global', 'prefix_system');
        $path = Strings::removePrefix($this->getPath(), $prefix);
        if (preg_match('#.+/$#', $path)) {
            $path = rtrim($path, '/');
        }
        return $path ?: '/';
    }

    public function getParam(string $param): ?string
    {
        $params = $this->getParams();
        return Arrays::getValue($params, $param);
    }

    public function getRawController(): ?string
    {
        $path = $this->pathProvider->getPath();
        $pathElements = $this->parsePath($path);
        return Arrays::firstOrNull($pathElements);
    }

    public function getController(): ?string
    {
        $rawController = $this->getRawController();
        return $rawController ? Strings::underscoreToCamelCase($rawController) : null;
    }

    public function getAction(): ?string
    {
        $path = $this->pathProvider->getPath();
        $pathElements = $this->parsePath($path);
        return Arrays::getValue($pathElements, 1);
    }

    private function parsePath(string $path = null, int $limit = null): array
    {
        if ($path != null) {
            $prefix = Config::getValue('global', 'prefix_system');
            $pathWithoutPrefix = urldecode(str_replace($prefix, '', $path));
            return preg_split('#/|\?#', $pathWithoutPrefix, $limit, PREG_SPLIT_NO_EMPTY);
        }
        return [];
    }

    public function getFullUrlWithPrefix(): string
    {
        return $this->pathProvider->getPath();
    }

    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function getRequestType(): string
    {
        $default = Arrays::getValue($_SERVER, 'REQUEST_METHOD', 'GET');
        return Arrays::getValue($_POST, '_method', $default);
    }

    public static function getRequestParameters($stream = 'php://input'): array
    {
        $parameters = self::parseRequest(stream_get_contents(fopen($stream, 'r')));
        return Arrays::toArray($parameters);
    }

    private static function parseRequest(string $content): array
    {
        $jsonParameters = self::jsonParameters($content);
        if ($jsonParameters) {
            return $jsonParameters;
        }

        $putParameters = self::putRequestParameters($content);
        if ($putParameters) {
            return $putParameters;
        }

        return [];
    }

    private static function jsonParameters(string $content): array
    {
        if (Strings::equalsIgnoreCase(ContentType::value(), 'application/json')) {
            return Arrays::toArray(Json::decode($content, true));
        }
        return [];
    }

    private static function putRequestParameters(string $content): array
    {
        if (
            Strings::equal(Arrays::getValue($_SERVER, 'REQUEST_METHOD'), 'PUT')
            && Strings::equalsIgnoreCase(ContentType::value(), 'application/x-www-form-urlencoded')
        ) {
            parse_str($content, $parameters);
            return Arrays::toArray($parameters);
        }
        return [];
    }

    public static function addPrefixIfNeeded(string $url): string
    {
        $prefixForGetMethod = Config::getValue('global', 'prefix_system_get');
        $prefix = $prefixForGetMethod ? $prefixForGetMethod : Config::getValue('global', 'prefix_system');
        $url = Strings::removePrefix($url, $prefix);
        return "{$prefix}{$url}";
    }

    public static function removePrefix(string $url): string
    {
        $prefix = Config::getValue('global', 'prefix_system');
        $prefixForGetMethod = Config::getValue('global', 'prefix_system_get');
        return Strings::removePrefix(Strings::removePrefix($url, $prefix), $prefixForGetMethod);
    }


    public static function getProtocol(): string
    {
        return (
            self::isServerVariableSetAndHasValue('HTTPS', ['on', 1]) ||
            self::isServerVariableSetAndHasValue('HTTP_X_FORWARDED_PROTO', 'https')
        ) ? 'https://' : 'http://';
    }

    private static function isServerVariableSetAndHasValue(string $variableName, array|string $values): bool
    {
        $value = Arrays::getValue($_SERVER, $variableName);
        return in_array($value, Arrays::toArray($values));
    }

    public static function getHost(): ?string
    {
        return Arrays::getValue($_SERVER, 'HTTP_HOST');
    }
}
