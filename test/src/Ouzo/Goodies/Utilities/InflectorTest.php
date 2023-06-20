<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Inflector;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class InflectorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Inflector::reset();
    }

    #[DataProvider('dataSampleWords')]
    public function testInflectingSingulars(string $singular, string $plural): void
    {
        $this->assertEquals($singular, Inflector::singularize($plural), "'{$plural}' should be singularized to '{$singular}'");
    }

    #[DataProvider('dataSampleWords')]
    public function testInflectingPlurals(string $singular, string $plural): void
    {
        $this->assertEquals($plural, Inflector::pluralize($singular), "'{$singular}' should be pluralized to '{$plural}'");
    }

    public static function dataSampleWords(): array
    {
        Inflector::reset();
        // in the format array('singular', 'plural')
        return [
            ['categoria', 'categorias'],
            ['house', 'houses'],
            ['powerhouse', 'powerhouses'],
            ['Bus', 'Buses'],
            ['bus', 'buses'],
            ['menu', 'menus'],
            ['news', 'news'],
            ['food_menu', 'food_menus'],
            ['Menu', 'Menus'],
            ['FoodMenu', 'FoodMenus'],
            ['quiz', 'quizzes'],
            ['matrix_row', 'matrix_rows'],
            ['matrix', 'matrices'],
            ['vertex', 'vertices'],
            ['index', 'indices'],
            ['Alias', 'Aliases'],
            ['Media', 'Media'],
            ['NodeMedia', 'NodeMedia'],
            ['alumnus', 'alumni'],
            ['bacillus', 'bacilli'],
            ['cactus', 'cacti'],
            ['focus', 'foci'],
            ['fungus', 'fungi'],
            ['nucleus', 'nuclei'],
            ['octopus', 'octopuses'],
            ['radius', 'radii'],
            ['stimulus', 'stimuli'],
            ['syllabus', 'syllabi'],
            ['terminus', 'termini'],
            ['virus', 'viri'],
            ['person', 'people'],
            ['glove', 'gloves'],
            ['crisis', 'crises'],
            ['tax', 'taxes'],
            ['wave', 'waves'],
            ['bureau', 'bureaus'],
            ['cafe', 'cafes'],
            ['roof', 'roofs'],
            ['foe', 'foes'],
            ['cookie', 'cookies'],
            ['', ''],
            ['foot', 'feet'],
        ];
    }

    #[Test]
    public function testCustomPluralRule()
    {
        Inflector::reset();
        Inflector::rules('plural', ['/^(custom)$/i' => '\1izables']);
        $this->assertEquals(Inflector::pluralize('custom'), 'customizables');

        Inflector::rules('plural', ['uninflected' => ['uninflectable']]);
        $this->assertEquals(Inflector::pluralize('uninflectable'), 'uninflectable');

        Inflector::rules('plural', [
            'rules' => ['/^(alert)$/i' => '\1ables'],
            'uninflected' => ['noflect', 'abtuse'],
            'irregular' => ['amaze' => 'amazable', 'phone' => 'phonezes']
        ]);
        $this->assertEquals(Inflector::pluralize('noflect'), 'noflect');
        $this->assertEquals(Inflector::pluralize('abtuse'), 'abtuse');
        $this->assertEquals(Inflector::pluralize('alert'), 'alertables');
        $this->assertEquals(Inflector::pluralize('amaze'), 'amazable');
        $this->assertEquals(Inflector::pluralize('phone'), 'phonezes');
    }

    #[Test]
    public function testCustomSingularRule()
    {
        Inflector::reset();
        Inflector::rules('singular', ['/(eple)r$/i' => '\1', '/(jente)r$/i' => '\1']);

        $this->assertEquals(Inflector::singularize('epler'), 'eple');
        $this->assertEquals(Inflector::singularize('jenter'), 'jente');

        Inflector::rules('singular', [
            'rules' => ['/^(bil)er$/i' => '\1', '/^(inflec|contribu)tors$/i' => '\1ta'],
            'uninflected' => ['singulars'],
            'irregular' => ['spins' => 'spinor']
        ]);

        $this->assertEquals(Inflector::singularize('inflectors'), 'inflecta');
        $this->assertEquals(Inflector::singularize('contributors'), 'contributa');
        $this->assertEquals(Inflector::singularize('spins'), 'spinor');
        $this->assertEquals(Inflector::singularize('singulars'), 'singulars');
    }

    #[Test]
    public function testRulesClearsCaches()
    {
        Inflector::reset();
        $this->assertEquals(Inflector::singularize('Bananas'), 'Banana');
        $this->assertEquals(Inflector::pluralize('Banana'), 'Bananas');

        Inflector::rules('singular', [
            'rules' => ['/(.*)nas$/i' => '\1zzz']
        ]);
        $this->assertEquals('Banazzz', Inflector::singularize('Bananas'), 'Was inflected with old rules.');

        Inflector::rules('plural', [
            'rules' => ['/(.*)na$/i' => '\1zzz'],
            'irregular' => ['corpus' => 'corpora']
        ]);
        $this->assertEquals(Inflector::pluralize('Banana'), 'Banazzz', 'Was inflected with old rules.');
        $this->assertEquals(Inflector::pluralize('corpus'), 'corpora', 'Was inflected with old irregular form.');
    }

    #[Test]
    public function testCustomRuleWithReset()
    {
        Inflector::reset();
        $uninflected = ['atlas', 'lapis', 'onibus', 'pires', 'virus', '.*x'];
        $pluralIrregular = ['as' => 'ases'];

        Inflector::rules('singular', [
            'rules' => ['/^(.*)(a|e|o|u)is$/i' => '\1\2l'],
            'uninflected' => $uninflected,
        ], true);

        Inflector::rules('plural', [
            'rules' => [
                '/^(.*)(a|e|o|u)l$/i' => '\1\2is',
            ],
            'uninflected' => $uninflected,
            'irregular' => $pluralIrregular
        ], true);

        $this->assertEquals(Inflector::pluralize('Alcool'), 'Alcoois');
        $this->assertEquals(Inflector::pluralize('Atlas'), 'Atlas');
        $this->assertEquals(Inflector::singularize('Alcoois'), 'Alcool');
        $this->assertEquals(Inflector::singularize('Atlas'), 'Atlas');
    }
}
