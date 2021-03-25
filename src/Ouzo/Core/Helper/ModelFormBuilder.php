<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Helper;

use Ouzo\Csrf\CsrfProtector;
use Ouzo\I18n;
use Ouzo\Utilities\Joiner;
use Ouzo\Utilities\Strings;

class ModelFormBuilder
{
    private object $object;

    public function __construct($object)
    {
        $this->object = $object;
    }

    private function objectName(): string
    {
        return Strings::camelCaseToUnderscore($this->object->getModelName());
    }

    private function generateId($name): string
    {
        return $this->objectName() . '_' . $name;
    }

    public function generateName($name): string
    {
        return $this->objectName() . '[' . $name . ']';
    }

    private function generatePredefinedAttributes(string $field): array
    {
        $id = $this->generateId($field);
        $attributes = ['id' => $id];

        if (in_array($field, $this->object->getErrorFields())) {
            $attributes['class'] = 'error';
        }
        return $attributes;
    }

    private function translate(string $field): string
    {
        return I18n::t($this->objectName() . '.' . $field);
    }

    public function label(string $field, array $options = []): string
    {
        return FormHelper::labelTag($this->generateId($field), $this->translate($field), $options);
    }

    public function textField(string $field, array $options = []): string
    {
        $attributes = $this->generatePredefinedAttributes($field);
        $attributes = $this->mergeAttributes($attributes, $options);
        return FormHelper::textFieldTag($this->generateName($field), $this->object->$field, $attributes);
    }

    public function textArea(string $field, array $options = []): string
    {
        $attributes = $this->generatePredefinedAttributes($field);
        $attributes = $this->mergeAttributes($attributes, $options);
        return FormHelper::textAreaTag($this->generateName($field), $this->object->$field, $attributes);
    }

    public function selectField(string $field, array $items, array $options = [], string $promptOption = null): string
    {
        $attributes = $this->generatePredefinedAttributes($field);
        $attributes = $this->mergeAttributes($attributes, $options);
        return FormHelper::selectTag($this->generateName($field), $items, [$this->object->$field], $attributes, $promptOption);
    }

    public function hiddenField(string $field, array $options = []): string
    {
        $attributes = $this->generatePredefinedAttributes($field);
        $attributes = $this->mergeAttributes($attributes, $options);
        return FormHelper::hiddenTag($this->generateName($field), $this->object->$field, $attributes);
    }

    public function passwordField(string $field, array $options = []): string
    {
        $attributes = $this->generatePredefinedAttributes($field);
        $attributes = $this->mergeAttributes($attributes, $options);
        return FormHelper::passwordFieldTag($this->generateName($field), $this->object->$field, $attributes);
    }

    public function checkboxField(string $field, array $options = []): string
    {
        $attributes = $this->generatePredefinedAttributes($field);
        $attributes = $this->mergeAttributes($attributes, $options);
        $value = $this->object->$field;
        $checked = !empty($value);
        return FormHelper::checkboxTag($this->generateName($field), '1', $checked, $attributes);
    }

    public function radioField(string $field, array $options = []): string
    {
        $attributes = $this->generatePredefinedAttributes($field);
        $attributes = $this->mergeAttributes($attributes, $options);
        return FormHelper::radioButtonTag($this->generateName($field), $this->object->$field, $attributes);
    }

    public function start(string $url, string $method = 'post', array $attributes = []): string
    {
        return FormHelper::formTag($url, $method, $attributes) . FormHelper::hiddenTag('csrftoken', CsrfProtector::getCsrfToken());
    }

    public function end(): string
    {
        return FormHelper::endFormTag();
    }

    public function getObject(): object
    {
        return $this->object;
    }

    private function mergeAttributes(array $attributes, array $options): array
    {
        if (isset($options['class']) && isset($attributes['class'])) {
            $options['class'] = Joiner::on(' ')->skipNulls()->join([$options['class'], $attributes['class']]);
        }
        return array_merge($attributes, $options);
    }
}
