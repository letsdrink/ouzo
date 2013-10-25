<?php
use Ouzo\Helper\AutoLabelModelFromBuilder;
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

function hiddenField($params)
{
    $name = $params['name'];
    $value = escapeText($params['value']);
    $id = isset($params['id']) ? $params['id'] : $params['name'];

    $hidden = hiddenTag($value, array('id' => $id, 'name' => $name));
    return <<<HTML
        $hidden
HTML;
}

function textField($label, $name, $value, $options = array())
{
    $label = escapeNewLine($label);
    $value = escapeText($value);

    $predefined = array('id' => '', 'new_row' => true, 'width' => null, 'label_width' => null, 'label_margin' => null, 'class' => null, 'error' => false, 'hide_label' => false);
    $id = Arrays::getValue($options, 'id', cleanHtmlId($name));
    $options = array_merge($predefined, $options);

    if (!Arrays::getValue($options, 'readonly')) {
        unset($options['readonly']);
    }

    $inputStyle = '';
    if (isset($options['width']) && $options['width'] > 0) {
        $inputStyle .= 'margin-left: 2px; width: ' . $options['width'] . 'px;';
    }

    $divClass = Arrays::getValue($options, 'class', '');
    $divClass .= $options['new_row'] ? ' field' : ' field-next';
    if ($options['error']) {
        $divClass .= ' field-with-error';
    }
    $divClass = trim($divClass);

    $labelStyle = array();
    if (isset($options['label_width']) && $options['label_width'] > 0) {
        $labelStyle = array('style' => 'margin-left: ' . $options['label_margin'] . 'px; width: ' . $options['label_width'] . 'px;');
    }

    $labelHtml = (strlen($label) > 0 && !$options['hide_label']) ? labelTag($id, $label, $labelStyle) : '';

    $attributes = array_diff_key($options, $predefined);

    $attr = array('id' => $id, 'name' => $name, 'style' => $inputStyle);
    $attr = array_merge($attr, $attributes);
    $input = textFieldTag($value, $attr);

    return <<<HTML
        <div class="$divClass">
            $labelHtml
            $input
        </div>
HTML;
}

function checkboxField($label, $name, $value, $checked, $options = array())
{
    $label = escapeNewLine($label);
    $value = escapeText($value);

    $id = Arrays::getValue($options, 'id', cleanHtmlId($name));

    $labelStyle = array();
    if (isset($options['label_width']) && $options['label_width'] > 0) {
        $margin = Arrays::getValue($options, 'label_margin');
        $labelStyle = array('style' => 'margin-left: ' . $margin . 'px; width: ' . $options['label_width'] . 'px;');
    }
    $labelHtml = labelTag($id, $label, $labelStyle);

    $attr = array('id' => $id, 'name' => $name);
    $input = checkboxTag($value, $checked, $attr);

    return <<<HTML
        <div class="field">
            $labelHtml
            $input
        </div>
HTML;
}

function cleanHtmlId($id)
{
    $id = str_replace('[', '_', $id);
    $id = str_replace(']', '', $id);
    return $id;
}

function textArea($label, $id, $value, array $size, array $options = array())
{
    $name = $id;
    $id = cleanHtmlId($id);
    $rows = $size['rows'];
    $cols = $size['cols'];
    $label = escapeNewLine($label);
    $value = escapeText($value);

    $inputStyle = '';
    if (isset($options['error']) && $options['error']) {
        $inputStyle .= 'border: 2px solid #ff0000;';
    }

    $labelTag = labelTag($id, $label);
    $texAreaTag = textAreaTag($value, array(
        'name' => $name,
        'id' => $id,
        'rows' => $rows,
        'cols' => $cols,
        'style' => $inputStyle
    ));
    return <<<HTML
        <div class="field">
            $labelTag
            $texAreaTag
        </div>
HTML;
}

function selectListHtml($label, $id, $name, array $items, array $selected, array $attr)
{
    $label = escapeNewLine($label);
    $labelHtml = $label ? labelTag($id, $label) : '';

    $attributes = array('id' => $id, 'name' => $name);
    $attributes = array_merge($attributes, $attr);
    $select = selectTag($items, $selected, $attributes);

    return <<<HTML
        <div class="field">
            $labelHtml
            $select
        </div>
HTML;
}

function selectField($label, $id, $value, array $options, $defaultOption = null, $size = 1)
{
    $name = $id;
    $id = cleanHtmlId($id);
    $label = escapeNewLine($label);
    $value = escapeText($value);

    if ($defaultOption !== null) {
        $options = array(null => $defaultOption) + $options;
    }

    return selectListHtml($label, $id, $name, $options, array($value), array('size' => $size));
}

function multiselectField($label, $name, array $selected, array $options, $config = array())
{
    $id = Arrays::getValue($config, 'id', cleanHtmlId($name));
    $name = "{$name}[]";
    $selectAttr = array('multiple' => 'multiple');
    if ($config['size'])
        $selectAttr['size'] = $config['size'];
    if ($config['class'])
        $selectAttr['class'] = $config['class'];
    return selectListHtml($label, $id, $name, $options, $selected, $selectAttr);
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

function selectTag(array $items = array(), $value, array $attributes = array())
{
    $attr = _prepareAttributes($attributes);
    $optionsString = '';
    foreach ($items as $optionValue => $optionName) {
        $optionsString .= optionTag($optionValue, $optionName, $value);
    }
    return '<select ' . $attr . '>' . $optionsString . '</select>';
}

function optionTag($value, $name, $current)
{
    $selected = Arrays::findKeyByValue($current, $value) !== false ? 'selected' : '';
    return '<option value="' . $value . '" ' . $selected . '>' . $name . '</option>';
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

function formFor($object, $objectName = null, array $options = array('auto_labels' => true))
{
    return $options['auto_labels'] ? new AutoLabelModelFromBuilder($object, $objectName) : new ModelFormBuilder($object);
}
