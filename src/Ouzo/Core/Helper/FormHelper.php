<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Helper\ModelFormBuilder;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

function escapeText($text)
{
    return htmlspecialchars($text);
}

function escapeNewLine($text)
{
    return Strings::escapeNewLines($text);
}

function linkTo($name, $href, $attributes = [])
{
    $attr = _prepareAttributes($attributes);
    return '<a href="' . escapeNewLine($href) . '" ' . $attr . '>' . escapeNewLine($name) . '</a>';
}

function linkButton($params)
{
    $defaultParams = ['class' => 'buttonLong'];
    $params = array_merge($defaultParams, $params);

    $url = $params['url'];
    if (!empty($params['params'])) {
        $url .= '/?';
        foreach ($params['params'] as $paramKey => $paramValue) {
            $url .= $paramKey . '=' . $paramValue;
        }
    }

    return <<<HTML
        <input type="button" class="${params['class']}" name="${params['name']}" id="${params['name']}" value="${params['value']}" onclick="window.location.href = '$url'"/>
HTML;
}

function formButton($params)
{
    $defaultParams = ['class' => 'buttonLong'];
    $params = array_merge($defaultParams, $params);
    $value = escapeText($params['value']);

    return <<<HTML
        <input type="button" name="${params['name']}" id="${params['name']}" value="$value" class="{$params['class']}"/>
HTML;
}

function translatableOptions($prefix, $options)
{
    $result = [];
    foreach ($options as $optionKey) {
        $result[$optionKey] = t($prefix . $optionKey);
    }
    return $result;
}

function labelTag($name, $content, array $attributes = [])
{
    $attr = _prepareAttributes($attributes);
    $attr = $attr ? ' ' . $attr : '';
    return '<label for="' . $name . '"' . $attr . '>' . escapeText($content) . '</label>';
}

function hiddenTag($name, $value, array $attributes = [])
{
    $attr = _prepareAttributes($attributes, ['name' => $name, 'value' => escapeText($value)]);
    return '<input type="hidden" ' . $attr . '/>';
}

function textFieldTag($name, $value, array $attributes = [])
{
    $attr = _prepareAttributes($attributes, ['name' => $name, 'value' => escapeText($value)]);
    return '<input type="text" ' . $attr . '/>';
}

function textAreaTag($name, $content, array $attributes = [])
{
    $attr = _prepareAttributes($attributes, ['name' => $name]);
    return '<textarea ' . $attr . '>' . escapeText($content) . '</textarea>';
}

function checkboxTag($name, $value, $checked, array $attributes = [])
{
    $attr = _prepareAttributes($attributes, ['name' => $name]);
    $workaround = '<input name="' . $name . '" type="hidden" value="0" />';
    return $workaround . '<input type="checkbox" value="' . $value . '" ' . $attr . ' ' . ($checked ? 'checked' : '') . '/>';
}

function selectTag($name, array $items = [], $value, array $attributes = [], $promptOption = null)
{
    $value = Arrays::toArray($value);
    $attr = _prepareAttributes($attributes, ['name' => $name]);
    $optionsString = '';
    if ($promptOption) {
        $items = [null => $promptOption] + $items;
    }
    foreach ($items as $optionValue => $optionName) {
        $optionsString .= optionTag($optionValue, $optionName, $value, Arrays::getValue($attributes, 'readonly') == 'readonly');
    }
    return '<select ' . $attr . '>' . $optionsString . '</select>';
}

function optionTag($value, $name, $current, $disabled)
{
    $selected = Arrays::findKeyByValue($current, $value) !== false;
    if ($selected) {
        $attribute = 'selected';
    } else {
        $attribute = $disabled ? 'disabled' : '';
    }

    $value = Strings::isNotBlank($value) ? ' value="' . $value . '" ' : ' value="" ';
    return '<option' . $value . $attribute . '>' . escapeText($name) . '</option>';
}

function passwordFieldTag($name, $value, array $attributes = [])
{
    $attr = _prepareAttributes($attributes, ['name' => $name, 'value' => escapeText($value)]);
    return '<input type="password" ' . $attr . '/>';
}

function radioButtonTag($name, $value, array $attributes = [])
{
    $attr = _prepareAttributes($attributes, ['name' => $name, 'value' => $value]);
    return '<input type="radio" ' . $attr . '/>';
}

function formTag($url, $method = 'POST', $attributes = [])
{
    $method = strtoupper($method);
    $workAroundTag = _methodWorkAroundTag($method);
    $method = _isUnsupportedMethod($method) ? 'POST' : $method;
    $attr = _prepareAttributes($attributes);
    $form = '<form ' . $attr . ' action="' . $url . '" method="' . $method . '">';
    $form .= $workAroundTag;
    return $form;
}

function endFormTag()
{
    return '</form>';
}

function _methodWorkAroundTag($method)
{
    if (_isUnsupportedMethod($method)) {
        return hiddenTag('_method', $method);
    }
    return '';
}

function _isUnsupportedMethod($method)
{
    return in_array($method, ['PUT', 'PATCH', 'DELETE']);
}

function _prepareAttributes(array $attributes = [], array $predefinedAttributes = [])
{
    if (isset($predefinedAttributes['name'])) {
        $predefinedAttributes = ['id' => $predefinedAttributes['name']] + $predefinedAttributes;
    }
    $attributes = array_merge($predefinedAttributes, $attributes);
    return _attributesToHtml($attributes);
}

function _attributesToHtml(array $attributes)
{
    $attr = '';
    foreach ($attributes as $opt_key => $opt_value) {
        $attr .= $opt_key . '="' . $opt_value . '" ';
    }
    return trim($attr);
}

function formFor($model)
{
    return new ModelFormBuilder($model);
}
