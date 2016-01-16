<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Injection\Annotation;

use Ouzo\Injection\InjectorException;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class DocCommentExtractor implements AnnotationMetadataProvider
{
    public function getMetadata($instance)
    {
        $class = new ReflectionClass($instance);
        $properties = $class->getProperties();
        $annotations = array();
        foreach ($properties as $property) {
            $doc = $property->getDocComment();
            if (Strings::contains($doc, '@Inject')) {
                if (preg_match("#@var ([\\\\A-Za-z0-9]*)#s", $doc, $matched)) {
                    $className = $matched[1];
                    $name = $this->extractName($doc);
                    $annotations[$property->getName()] = array('name' => $name, 'className' => $className);
                } else {
                    throw new InjectorException('Cannot @Inject dependency. @var is not defined for property $' . $property->getName() . ' in class ' . $class->getName() . '.');
                }
            }
        }
        return $annotations;
    }

    private function extractName($doc)
    {
        if (preg_match("#@Named\\(\"([A-Za-z0-9_]*)\"\\)#s", $doc, $matched)) {
            return $matched[1];
        }
        return '';
    }
}
