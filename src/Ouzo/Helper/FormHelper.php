<?php
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

function linkTo($name, $href, $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    return '<a href="' . escapeNewLine($href) . '" ' . $attr . '>' . escapeNewLine($name) . '</a>';
}

function linkButton($params)
{
    $defaultParams = array('class' => 'buttonLong');
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
    $defaultParams = array('class' => 'buttonLong');
    $params = array_merge($defaultParams, $params);
    $value = escapeText($params['value']);

    return <<<HTML
        <input type="button" name="${params['name']}" id="${params['name']}" value="$value" class="{$params['class']}"/>
HTML;
}

function translatableOptions($prefix, $options)
{
    $result = array();
    foreach ($options as $optionKey) {
        $result[$optionKey] = t($prefix . $optionKey);
    }
    return $result;
}

function labelTag($id, $name, array $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    $attr = $attr ? ' ' . $attr : '';
    return '<label for="' . $id . '"' . $attr . '>' . $name . '</label>';
}

function hiddenTag($value, array $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    return '<input type="hidden" value="' . $value . '" ' . $attr . '/>';
}

function textFieldTag($value, array $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    return '<input type="text" value="' . $value . '" ' . $attr . '/>';
}

function textAreaTag($value, array $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    return '<textarea ' . $attr . '>' . $value . '</textarea>';
}

function checkboxTag($value, $checked, array $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    $workaround = '<input name="' . $attributes['name'] . '" type="hidden" value="0" />';
    return $workaround . '<input type="checkbox" value="' . $value . '" ' . $attr . ' ' . ($checked ? 'checked' : '') . '/>';
}

function selectTag(array $items = array(), $value, array $attributes = array(), $defaultOption = null)
{
    $attr = _prepareAttributes($attributes);
    $optionsString = '';
    if ($defaultOption) {
        $items = array(null => $defaultOption) + $items;
    }
    foreach ($items as $optionValue => $optionName) {
        $optionsString .= optionTag($optionValue, $optionName, $value);
    }
    return '<select ' . $attr . '>' . $optionsString . '</select>';
}

function optionTag($value, $name, $current)
{
    $selected = Arrays::findKeyByValue($current, $value) !== false ? 'selected' : '';
    $value = $value ? ' value="' . $value . '" ' : '';
    return '<option' . $value . '' . $selected . '>' . $name . '</option>';
}

function passwordFieldTag($value, array $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    return '<input type="password" value="' . $value . '" ' . $attr . '/>';
}

function formTag($url, $method = 'POST', $attributes = array())
{
    $method = strtoupper($method);
    $workAroundTag = _methodWorkAroundTag($method);
    $method = _isUnsupportedMethod($method) ? 'POST' : $method;
    $attr = _prepareAttributes($attributes);
    $form = '<form ' . $attr . ' action="' . $url . '" method="' . $method . '">';
    $form .= $workAroundTag;
    return $form;
}

function endTag()
{
    return '</form>';
}

function _methodWorkAroundTag($method)
{
    if (_isUnsupportedMethod($method)) {
        return hiddenTag($method, array('name' => '_method'));
    }
    return '';
}

function _isUnsupportedMethod($method)
{
    return in_array($method, array('PUT', 'PATCH', 'DELETE'));
}

function _prepareAttributes(array $attributes = array())
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

function formForCustomBuilder($formBuilder)
{
    return $formBuilder;
}