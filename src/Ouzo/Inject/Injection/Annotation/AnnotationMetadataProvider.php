<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection\Annotation;

use ReflectionClass;

interface AnnotationMetadataProvider
{
    /**
     * @param ReflectionClass $class
     * @param bool $privateMethodsOnly
     * @return mixed array that contains properties metadata: 'property_name' => ['className' => '\Class', 'name' => 'Name']
     */
    public function getMetadata(ReflectionClass $class, $privateMethodsOnly = false);

    /**
     * @param string $className
     * @return mixed array that contains ordered constructor arguments metadata: '0' => ['className' => '\Class', 'name' => 'Name']
     */
    public function getConstructorMetadata($className);
}
