<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Uri;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class UriGeneratorTemplates
{
    public static function replaceTemplate(string $namesList, string $methodsList, string $globalFunctionsList): string
    {
        $template = UriGeneratorTemplates::classTemplate();
        $template = self::replace($template, 'METHODS', $methodsList);
        $template = self::replace($template, 'URI_NAMES', $namesList);
        return self::replace($template, 'GLOBAL_FUNCTIONS', $globalFunctionsList);
    }

    private static function replace(string $template, string $pattern, ?string $replacement): string
    {
        $replacement ??= Strings::EMPTY;
        return str_replace(["/*{{$pattern}}*/", "%{{$pattern}}"], $replacement, $template);
    }

    public static function method(string $controller, $action, string $name, array $parameters, string $url): string
    {
        $template = UriGeneratorTemplates::methodTemplate();
        return self::generateFunction($template, $controller, $action, $name, $parameters, $url);
    }

    public static function function (string $controller, $action, string $name, array $parameters, string $url): string
    {
        $template = UriGeneratorTemplates::functionTemplate();
        return self::generateFunction($template, $controller, $action, $name, $parameters, $url);
    }

    private static function generateFunction(string $template, string $controller, $action, ?string $name, array $parameters, string $url): string
    {
        $checkParametersStatement = self::generateCheckStatement($parameters);
        $template = self::replace($template, 'CONTROLLER', $controller);
        $template = self::replace($template, 'ACTION', $action);
        $template = self::replace($template, 'NAME', $name);
        $template = self::replace($template, 'CHECK_PARAMETERS', $checkParametersStatement);
        $template = self::replace($template, 'PARAMS', implode(", ", $parameters));
        return self::replace($template, 'URI', $url);
    }

    private static function generateCheckStatement(array $parameters): string
    {
        $checkParameters = Arrays::map($parameters, fn($param) => self::checkParameterTemplate($param));
        return implode('', $checkParameters);
    }

    private static function classTemplate(): string
    {
        return /** @lang InjectablePHP */ <<<'TEMPLATE'
<?php
class GeneratedUriHelper {
    
    private static function validateParameter(mixed $parameter): void
    {
        if (!isset($parameter)) {
            throw new \InvalidArgumentException("Missing parameters");
        }
    }
    
/*{METHODS}*/
    public static function allGeneratedUriNames(): array 
    {
        return [/*{URI_NAMES}*/];
    }
}

/*{GLOBAL_FUNCTIONS}*/
function allGeneratedUriNames(): array
{
    return GeneratedUriHelper::allGeneratedUriNames();
}
TEMPLATE;
    }

    private static function methodTemplate(): string
    {
        return <<<'TEMPLATE'
    /**
     * @see %{CONTROLLER}::%{ACTION}()
     */
    public static function %{NAME}(%{PARAMS}): string 
    {
        %{CHECK_PARAMETERS}return "%{URI}";
    }
    
TEMPLATE;
    }

    private static function functionTemplate(): string
    {
        return <<<'TEMPLATE'
/**
 * @see %{CONTROLLER}::%{ACTION}()
 */
function %{NAME}(%{PARAMS}): string 
{
    return GeneratedUriHelper::%{NAME}(%{PARAMS});
}

TEMPLATE;
    }

    private static function checkParameterTemplate(string $param): string
    {
        return <<<TEMPLATE
GeneratedUriHelper::validateParameter($param);
        
TEMPLATE;
    }
}
