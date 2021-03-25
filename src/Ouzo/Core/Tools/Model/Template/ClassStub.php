<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Path;

class ClassStub
{
    const FIELDS_COUNT_IN_LINE = 7;

    private string $stubContent;
    private array $attributes = [];
    private array $placeholderWithReplacements = [];

    public function __construct()
    {
        $stubFilePath = Path::join(__DIR__, 'stubs', 'class.stub');
        $this->stubContent = file_get_contents($stubFilePath);
    }

    public function addColumn(DatabaseColumn $databaseColumn): static
    {
        $this->attributes[$databaseColumn->name] = $databaseColumn->type;
        return $this;
    }

    public function addPlaceholderReplacement(string $placeholder, string $replacement): static
    {
        $this->placeholderWithReplacements[$placeholder] = $replacement;
        return $this;
    }

    public function addTableSetupItem(string $name, string $value): void
    {
        if ($value) {
            $value = sprintf("'%s' => '%s',", $name, $value);
        }
        $this->addPlaceholderReplacement("table_{$name}", $value);
    }

    public function addTablePrimaryKey(string $primaryKey): void
    {
        if (empty($primaryKey)) {
            $value = sprintf("'%s' => '',", 'primaryKey');
            $this->addPlaceholderReplacement("table_primaryKey", $value);
        } else {
            $placeholderPrimaryKey = ($primaryKey != 'id') ? $primaryKey : '';
            $this->addTableSetupItem('primaryKey', $placeholderPrimaryKey);
        }
    }

    public function replacePlaceholders(array $replacement): static
    {
        foreach ($replacement as $key => $value) {
            $searchRegExp = ($value) ? "/{($key)}/" : "/\s*{($key)}*/";
            $this->stubContent = preg_replace($searchRegExp, $value, $this->stubContent);
        }
        return $this;
    }

    public function getPropertiesAsString(): string
    {
        $properties = FluentArray::from($this->attributes)
            ->mapEntries(fn($name, $type) => " * @property {$type} {$name}")
            ->toArray();
        return implode("\n", $properties);
    }

    public function getFieldsAsString(): string
    {
        $fields = array_keys($this->attributes);
        $escapedFields = Arrays::map($fields, Functions::compose(Functions::append("'"), Functions::prepend("'")));
        for ($index = self::FIELDS_COUNT_IN_LINE; $index < sizeof($escapedFields); $index += self::FIELDS_COUNT_IN_LINE) {
            $escapedFields[$index] = "\n\t\t\t{$escapedFields[$index]}";
        }
        return implode(', ', $escapedFields);
    }

    private function getPlaceholderReplacements(): array
    {
        $this
            ->addPlaceholderReplacement('properties', $this->getPropertiesAsString())
            ->addPlaceholderReplacement('fields', $this->getFieldsAsString());
        return $this->placeholderWithReplacements;
    }

    public function contents(): string
    {
        $this->replacePlaceholders($this->getPlaceholderReplacements());
        return $this->stubContent;
    }
}
