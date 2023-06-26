<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Helper;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class HtmlElementRenderer
{
    private string $element;
    private array $attributes = [];
    private bool $selfClosing;
    private string $text = '';
    private array $flags = [];
    private bool $closeTag;

    public function __construct(
        string $element,
        bool $selfClosing = false,
        bool $closeTag = true
    )
    {
        $this->element = $element;
        $this->selfClosing = $selfClosing;
        $this->closeTag = $closeTag;
    }

    public static function element(string $element, bool $selfClosing = false, bool $closeTag = true): HtmlElementRenderer
    {
        return new HtmlElementRenderer($element, $selfClosing, $closeTag);
    }

    public static function anchor(): HtmlElementRenderer
    {
        return new HtmlElementRenderer("a");
    }

    public static function input(string $type): HtmlElementRenderer
    {
        return (new HtmlElementRenderer("input", true))
            ->setAttribute("type", $type);
    }

    public static function label(): HtmlElementRenderer
    {
        return new HtmlElementRenderer("label");
    }

    public static function textarea(): HtmlElementRenderer
    {
        return new HtmlElementRenderer("textarea");
    }

    public static function select(): HtmlElementRenderer
    {
        return new HtmlElementRenderer("select");
    }

    public static function formStart(): HtmlElementRenderer
    {
        return new HtmlElementRenderer("form", false, false);
    }

    public function setText(?string $text): HtmlElementRenderer
    {
        $this->text = htmlspecialchars($text ?? Strings::EMPTY, ENT_COMPAT);
        return $this;
    }

    public function setId(string $id): HtmlElementRenderer
    {
        return $this->setAttribute("id", $id);
    }

    public function setName(string $name): HtmlElementRenderer
    {
        return $this->setAttribute("name", $name);
    }

    public function setNameId(string $name): HtmlElementRenderer
    {
        $this->setName($name);
        return $this->setId($name);
    }

    public function setClass(?string $class): HtmlElementRenderer
    {
        return $this->setAttribute("class", $class);
    }

    public function setValue(?string $value): HtmlElementRenderer
    {
        return $this->setAttribute("value", $value);
    }

    public function setFlag(string $flag, ?bool $isSet): HtmlElementRenderer
    {
        if ($isSet) {
            $this->flags[] = $flag;
        }
        return $this;
    }

    public function setDisabled(?bool $disabled): HtmlElementRenderer
    {
        return $this->setFlag("disabled", $disabled);
    }

    public function setHtmlContent(string $html): HtmlElementRenderer
    {
        $this->text = $html;
        return $this;
    }

    public function setAttribute(string $attribute, mixed $value): HtmlElementRenderer
    {
        $this->attributes[$attribute] = $value;
        return $this;
    }

    public function setAttributes(array $attributes): HtmlElementRenderer
    {
        $name = Arrays::getValue($attributes, 'name');
        $id = Arrays::getValue($attributes, 'id');
        if ($name && empty($id)) {
            $attributes[] = ["id" => ""];
        }
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    public function render(): string
    {
        $attrs = $this->attributesToHtml($this->attributes);

        $html = "<{$this->element}";
        if ($attrs) {
            $html .= " " . $attrs;
        }

        $flags = implode(" ", $this->flags);
        if ($flags) {
            $html .= " " . $flags;
        }

        if ($this->selfClosing) {
            $html .= "/>";
        } else {
            $html .= ">";
            if ($this->text) {
                $html .= $this->text;
            }
            if ($this->closeTag) {
                $html .= "</{$this->element}>";
            }
        }

        return $html;
    }

    private function attributesToHtml(array $attributes): string
    {
        $attributes = $this->sort($attributes);

        $attrs = Arrays::mapEntries($attributes, function ($key, $value) {
            $escaped = htmlspecialchars($value ?? Strings::EMPTY, ENT_COMPAT);
            return "$key=\"$escaped\"";
        });
        return implode(' ', $attrs);
    }

    private function sort(array $attributes): array
    {
        $fixedAttrOrder = [
            'type', 'id', 'name', 'class', 'value'
        ];
        $fixedOrderAttrs = [];

        foreach ($fixedAttrOrder as $attr) {
            if (Arrays::keyExists($attributes, $attr)) {
                $fixedOrderAttrs[$attr] = $attributes[$attr];
            }
        }

        $alphabeticOrderAttrs = Arrays::filterByAllowedKeys($attributes, array_diff(array_keys($attributes), $fixedAttrOrder));
        ksort($alphabeticOrderAttrs);
        return $fixedOrderAttrs + $alphabeticOrderAttrs;
    }
}
