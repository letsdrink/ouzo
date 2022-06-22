<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Injection\Annotation\InjectList;

class ClassWithListOfConstructorDep
{
    #[Inject]
    public function __construct(#[InjectList(SampleInterface::class)] private array $sampleInterfaces)
    {
    }

    public function getSampleInterfaces(): array
    {
        return $this->sampleInterfaces;
    }
}
