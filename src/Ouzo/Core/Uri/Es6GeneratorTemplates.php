<?php

namespace Ouzo\Uri;

class Es6GeneratorTemplates
{
    public static function getFunction(string $name, $parameters, string $return)
    {
        $functionTemplate = empty($parameters) ? self::funcWithoutArgs() : self::functionWithArgs();
        $replacements = [
            "NAME" => $name,
            "ARGS" => implode(", ", $parameters),
            "RETURN" => $return
        ];
        return self::replace($functionTemplate, $replacements);
    }

    private static function replace(string $template, array $replacements)
    {
        $result = $template;
        foreach ($replacements as $key => $value) {
            $result = str_replace("${key}_REPLACEMENT", $value, $result);
        }
        return $result;
    }

    public static function checkParametersTemplate(): string
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

    private static function funcWithoutArgs()
    {
        return /** @lang ECMAScript 6 */ <<<'TEMPLATE'
const NAME_REPLACEMENT = () => 'RETURN_REPLACEMENT'
TEMPLATE;
    }

    private static function functionWithArgs()
    {
        return /** @lang ECMAScript 6 */ <<<'TEMPLATE'
const NAME_REPLACEMENT = (ARGS_REPLACEMENT) => {
    checkParameters(ARGS_REPLACEMENT)
    return 'RETURN_REPLACEMENT'
}
TEMPLATE;
    }
}