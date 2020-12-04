<?php

namespace Ouzo\Uri;

use Closure;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class Es6GeneratorTemplates
{
    private string $format;

    public function __construct(string $format)
    {
        $this->format = $format;
        if ($format != 'js' && $format != 'ts') {
            throw new \InvalidArgumentException("Unsupported output format '$format'.");
        }
    }

    public function getFunction(string $name, $parameters, string $return)
    {
        $functionTemplate = empty($parameters) ? $this->functionWithoutArgs() : $this->functionWithArgs();
        $replacements = [
            "NAME" => $name,
            "ARGS_DEFINITION" => $this->prepareParametersDefinition($parameters),
            "ARGS" => implode(", ", $parameters),
            "RETURN" => $return
        ];
        return $this->replace($functionTemplate, $replacements);
    }

    private function prepareParametersDefinition($parameters)
    {
        return $this->run(fn() => $this->prepareParametersDefinitionJs($parameters), fn() => $this->prepareParametersDefinitionTs($parameters));
    }

    private function prepareParametersDefinitionJs($parameters): string
    {
        return implode(", ", $parameters);
    }

    private function prepareParametersDefinitionTs($parameters): string
    {
        return implode(", ", Arrays::map($parameters, Functions::append(": UriParam")));
    }

    private function replace(string $template, array $replacements)
    {
        $result = $template;
        foreach ($replacements as $key => $value) {
            $result = str_replace("${key}_REPLACEMENT", $value, $result);
        }
        return $result;
    }

    public function checkParametersTemplate(): string
    {
        return $this->run(fn() => $this->checkParametersTemplateJs(), fn() => $this->checkParametersTemplateTs());
    }

    private function checkParametersTemplateJs(): string
    {
        return /** @lang ECMAScript 6 */ <<<'TEMPLATE'
const checkParameters = (...args) => {
    args.forEach(arg => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}
TEMPLATE;
    }

    private function checkParametersTemplateTs(): string
    {
        return /** @lang TypeScript */ <<<'TEMPLATE'
type UriParam = string | number

const checkParameters = (...args: UriParam[]): void => {
    args.forEach((arg: UriParam) => {
        if (typeof arg !== 'string' && typeof arg !== 'number') {
            throw new Error("Uri helper: Bad parameters")
        }
    })
}
TEMPLATE;
    }

    private function functionWithoutArgs(): string
    {
        return $this->run(fn() => $this->functionWithoutArgsJs(), fn() => $this->functionWithoutArgsTs());
    }

    private function functionWithoutArgsJs(): string
    {
        return /** @lang ECMAScript 6 */ <<<'TEMPLATE'
export const NAME_REPLACEMENT = () => 'RETURN_REPLACEMENT'
TEMPLATE;
    }

    private function functionWithoutArgsTs(): string
    {
        return /** @lang TypeScript */ <<<'TEMPLATE'
export const NAME_REPLACEMENT = (): string => 'RETURN_REPLACEMENT'
TEMPLATE;
    }

    private function functionWithArgs(): string
    {
        return $this->run(fn() => $this->functionWithArgsJs(), fn() => $this->functionWithArgsTs());
    }

    private function functionWithArgsJs(): string
    {
        return /** @lang ECMAScript 6 */ <<<'TEMPLATE'
export const NAME_REPLACEMENT = (ARGS_DEFINITION_REPLACEMENT) => {
    checkParameters(ARGS_REPLACEMENT)
    return 'RETURN_REPLACEMENT'
}
TEMPLATE;
    }

    private function functionWithArgsTs()
    {
        return /** @lang TypeScript */ <<<'TEMPLATE'
export const NAME_REPLACEMENT = (ARGS_DEFINITION_REPLACEMENT): void => {
    checkParameters(ARGS_REPLACEMENT)
    return 'RETURN_REPLACEMENT'
}
TEMPLATE;
    }

    private function run(Closure $jsHelper, Closure $tsHelper)
    {
        if ($this->format === 'js') {
            return $jsHelper();
        }
        if ($this->format === 'ts') {
            return $tsHelper();
        }
        return null;
    }

}