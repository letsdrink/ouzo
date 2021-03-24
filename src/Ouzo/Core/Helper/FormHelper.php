<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Helper {

    use Ouzo\I18n;
    use Ouzo\Utilities\Arrays;
    use Ouzo\Utilities\Functions;
    use Ouzo\Utilities\Strings;

    class FormHelper
    {
        public static function escapeText(?string $text): ?string
        {
            return htmlspecialchars($text);
        }

        public static function escapeNewLine(?string $text): ?string
        {
            return Strings::escapeNewLines($text);
        }

        public static function linkTo(string $name, string $href, array $attributes = []): string
        {
            return HtmlElementRenderer::anchor()
                ->setAttribute("href", $href)
                ->setText($name)
                ->setAttributes($attributes)
                ->render();
        }

        public static function linkButton(array $params): string
        {
            $defaultParams = ['class' => 'buttonLong'];
            $params = array_merge($defaultParams, $params);
            $url = Arrays::getValue($params, 'url');
            $params = Arrays::getValue($params, 'params');
            if (!empty($params)) {
                $query = http_build_query($params);
                $url .= "/?" . $query;
            }

            return HtmlElementRenderer::input("button")
                ->setNameId($params["name"])
                ->setClass($params["class"])
                ->setValue($params["value"])
                ->setAttribute("onclick", "window.location.href = '$url'")
                ->render();
        }

        public static function formButton(array $params): string
        {
            $defaultParams = ['class' => 'buttonLong'];
            $params = array_merge($defaultParams, $params);

            return HtmlElementRenderer::input("button")
                ->setNameId($params["name"])
                ->setClass($params["class"])
                ->setValue($params["value"])
                ->render();
        }

        public static function translatableOptions(string $prefix, array $options): array
        {
            return Arrays::toMap($options, Functions::identity(), fn($key) => I18n::t($prefix . $key));
        }

        public static function labelTag(string $name, ?string $content, array $attributes = []): string
        {
            return HtmlElementRenderer::label()
                ->setAttributes($attributes)
                ->setAttribute("for", $name)
                ->setText($content)
                ->render();
        }

        public static function hiddenTag(string $name, ?string $value, array $attributes = []): string
        {
            return HtmlElementRenderer::input("hidden")
                ->setNameId($name)
                ->setAttributes($attributes)
                ->setValue($value)
                ->render();
        }

        public static function textFieldTag(string $name, ?string $value, array $attributes = []): string
        {
            return HtmlElementRenderer::input("text")
                ->setNameId($name)
                ->setAttributes($attributes)
                ->setValue($value)
                ->render();
        }

        public static function textAreaTag(string $name, ?string $content, array $attributes = []): string
        {
            return HtmlElementRenderer::textarea()
                ->setNameId($name)
                ->setAttributes($attributes)
                ->setText($content)
                ->render();
        }

        public static function checkboxTag(string $name, ?string $value, ?bool $checked, array $attributes = []): string
        {
            $uncheckedSubmitWorkaroundInput = HtmlElementRenderer::input("hidden")
                ->setName($name)
                ->setValue("0")
                ->render();

            return $uncheckedSubmitWorkaroundInput .
                HtmlElementRenderer::input("checkbox")
                    ->setNameId($name)
                    ->setAttributes($attributes)
                    ->setValue($value)
                    ->setFlag("checked", $checked)
                    ->render();
        }

        public static function selectTag(string $name, array $items = [], mixed $value = null, array $attributes = [], string $promptOption = null): string
        {
            $value = Arrays::toArray($value);

            if ($promptOption) {
                $items = [null => $promptOption] + $items;
            }

            $disabled = Arrays::getValue($attributes, 'readonly') == 'readonly';
            $items = Arrays::mapEntries($items, fn($optionValue, $text) => FormHelper::optionTag($optionValue, $text, $value, $disabled));
            $itemsHtml = implode("\n", $items);
            $itemsHtml = $itemsHtml ? "\n{$itemsHtml}\n" : $itemsHtml;
            return HtmlElementRenderer::select()
                ->setNameId($name)
                ->setAttributes($attributes)
                ->setHtmlContent($itemsHtml)
                ->render();
        }

        public static function optionTag(?string $value, ?string $text, array $selectedValue, ?bool $disabled): string
        {
            $selected = Arrays::findKeyByValue($selectedValue, $value) !== false;

            return HtmlElementRenderer::element("option", false)
                ->setText($text)
                ->setFlag("selected", $selected)
                ->setDisabled($disabled && !$selected)
                ->setValue($value)
                ->render();
        }

        public static function passwordFieldTag(string $name, ?string $value, array $attributes = []): string
        {
            return HtmlElementRenderer::input("password")
                ->setNameId($name)
                ->setAttributes($attributes)
                ->setValue($value)
                ->render();
        }

        public static function radioButtonTag(string $name, ?string $value, array $attributes = []): string
        {
            return HtmlElementRenderer::input("radio")
                ->setNameId($name)
                ->setAttributes($attributes)
                ->setValue($value)
                ->render();
        }

        public static function formTag(?string $url, string $method = 'POST', array $attributes = []): string
        {
            $method = strtoupper($method);
            $workAroundTag = FormHelper::methodWorkAroundTag($method);
            $method = FormHelper::isUnsupportedMethod($method) ? 'POST' : $method;
            return HtmlElementRenderer::formStart()
                    ->setAttributes($attributes)
                    ->setAttribute("action", $url)
                    ->setAttribute("method", $method)
                    ->render() . $workAroundTag;
        }

        public static function endFormTag(): string
        {
            return '</form>';
        }

        public static function methodWorkAroundTag(string $method): string
        {
            if (FormHelper::isUnsupportedMethod($method)) {
                return FormHelper::hiddenTag('_method', $method);
            }
            return '';
        }

        private static function isUnsupportedMethod(string $method): bool
        {
            return in_array($method, ['PUT', 'PATCH', 'DELETE']);
        }

        public static function formFor(mixed $model): ModelFormBuilder
        {
            return new ModelFormBuilder($model);
        }
    }

    function delegateToFormHelper(string $function, array $args)
    {
        return call_user_func_array([FormHelper::class, $function], $args);
    }
}

namespace {

    use JetBrains\PhpStorm\Deprecated;
    use Ouzo\Helper\FormHelper;
    use Ouzo\Helper\ModelFormBuilder;

    #[Deprecated(replacement: "FormHelper::escapeText()")]
    function escapeText(?string $text): ?string
    {
        return FormHelper::escapeText($text);
    }

    #[Deprecated(replacement: "FormHelper::escapeNewLine()")]
    function escapeNewLine(?string $text): ?string
    {
        return FormHelper::escapeNewLine($text);
    }

    #[Deprecated(replacement: "FormHelper::linkTo()")]
    function linkTo(string $name, string $href, array $attributes = []): string
    {
        return FormHelper::linkTo($name, $href, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::linkButton()")]
    function linkButton(array $params): string
    {
        return FormHelper::linkButton($params);
    }

    #[Deprecated(replacement: "FormHelper::linkButton()")]
    function formButton(array $params): string
    {
        return FormHelper::formButton($params);
    }

    #[Deprecated(replacement: "FormHelper::translatableOptions()")]
    function translatableOptions(string $prefix, array $options): array
    {
        return FormHelper::translatableOptions($prefix, $options);
    }

    #[Deprecated(replacement: "FormHelper::labelTag()")]
    function labelTag(string $name, string $content, array $attributes = []): string
    {
        return FormHelper::labelTag($name, $content, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::hiddenTag()")]
    function hiddenTag(string $name, string $value, array $attributes = []): string
    {
        return FormHelper::hiddenTag($name, $value, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::textFieldTag()")]
    function textFieldTag(string $name, string $value, array $attributes = []): string
    {
        return FormHelper::textFieldTag($name, $value, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::textAreaTag()")]
    function textAreaTag(string $name, string $content, array $attributes = []): string
    {
        return FormHelper::textAreaTag($name, $content, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::checkboxTag()")]
    function checkboxTag(string $name, string $value, ?bool $checked, array $attributes = []): string
    {
        return FormHelper::checkboxTag($name, $value, $checked, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::selectTag()")]
    function selectTag(string $name, array $items = [], mixed $value = null, array $attributes = [], string $promptOption = null): string
    {
        return FormHelper::selectTag($name, $items, $value, $attributes, $promptOption);
    }

    #[Deprecated(replacement: "FormHelper::optionTag()")]
    function optionTag(string $value, string $name, array $current, ?bool $disabled): string
    {
        return FormHelper::optionTag($value, $name, $current, $disabled);
    }

    #[Deprecated(replacement: "FormHelper::passwordFieldTag()")]
    function passwordFieldTag(string $name, string $value, array $attributes = []): string
    {
        return FormHelper::passwordFieldTag($name, $value, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::radioButtonTag()")]
    function radioButtonTag(string $name, string $value, array $attributes = []): string
    {
        return FormHelper::radioButtonTag($name, $value, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::formTag()")]
    function formTag(string $url, string $method = 'POST', array $attributes = []): string
    {
        return FormHelper::formTag($url, $method, $attributes);
    }

    #[Deprecated(replacement: "FormHelper::endFormTag()")]
    function endFormTag(): string
    {
        return FormHelper::endFormTag();
    }

    #[Deprecated(replacement: "FormHelper::formFor()")]
    function formFor(mixed $model): ModelFormBuilder
    {
        return FormHelper::formFor($model);
    }
}