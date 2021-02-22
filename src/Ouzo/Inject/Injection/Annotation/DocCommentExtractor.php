<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Annotation;

use Ouzo\Injection\InjectorException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

class DocCommentExtractor implements AnnotationMetadataProvider
{
    const ALL_PROPERTIES = ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED;

    /**
     * @inheritdoc
     */
    public function getMetadata(ReflectionClass $class, $privatePropertiesOnly = false)
    {
        $annotations = [];
        $properties = $class->getProperties($privatePropertiesOnly ? ReflectionProperty::IS_PRIVATE : self::ALL_PROPERTIES);
        foreach ($properties as $property) {
            $doc = $this->getDocCommentFrom($property);
            if (Strings::contains($doc, '@Inject')) {
                $className = $this->extractClass($class, $doc, $property);
                $name = $this->extractNamed($doc);
                $annotations[$property->getName()] = ['name' => $name, 'className' => $className];
            }
        }
        return $annotations;
    }

    /**
     * @inheritdoc
     */
    public function getConstructorMetadata($className)
    {
        $annotations = [];
        $instance = new ReflectionClass($className);
        $constructor = $instance->getConstructor();
        if ($constructor) {
            $doc = $this->getDocCommentFrom($constructor);
            if (Strings::contains($doc, '@Inject')) {
                $parameters = $constructor->getParameters();
                $namedMap = $this->extractNamedMap($parameters, $doc);
                foreach ($parameters as $parameter) {
                    $type = $parameter->getType();
                    if (!$type || !($type instanceof ReflectionNamedType)) {
                        throw new InjectorException("Cannot @Inject by constructor for class $className. All arguments should have types defined (but not union types!).");
                    }
                    $parameterName = $parameter->getName();
                    $name = Arrays::getValue($namedMap, $parameterName, '');

                    $annotations[$parameterName] = ['name' => $name, 'className' => $type->getName()];
                }
            }
        }
        return $annotations;
    }

    /**
     * Override when required by PHP encoder
     * @param Object $object
     * @return string
     */
    protected function getDocCommentFrom($object)
    {
        return $object->getDocComment();
    }

    /**
     * @param string $doc
     * @return string
     */
    private function extractNamed($doc)
    {
        if (preg_match("#@Named\\(\"([A-Za-z0-9_]*)\"\\)#s", $doc, $matched)) {
            return $matched[1];
        }
        return '';
    }

    private function extractClass(ReflectionClass $class, string $doc, ReflectionProperty $property): string
    {
        if ($property->hasType()) {
            return $property->getType()->getName();
        }
        if (preg_match("#@var ([\\\\A-Za-z0-9]*)#s", $doc, $matched)) {
            return Strings::removePrefix($matched[1], "\\");
        }
        throw new InjectorException('Cannot @Inject dependency - missing type. Use typed property or @var doc comment for property $' . $property->getName() . ' in class ' . $class->getName() . '.');
    }

    /**
     * @param array $parameters
     * @param string $doc
     * @return array
     */
    private function extractNamedMap($parameters, $doc)
    {
        if (preg_match('/@Named\("([A-Za-z0-9_,=]*)"\)/s', $doc, $matched)) {
            $paramsWithNamed = explode(',', $matched[1]);

            $paramsWithNamedAsArray = Arrays::map($paramsWithNamed, function ($paramWithName) {
                return explode('=', $paramWithName);
            });

            // Handle case: @Named("some_name")
            if (count($paramsWithNamed) == 1 && count($paramsWithNamedAsArray[0]) == 1) {
                return [$parameters[0]->getName() => $paramsWithNamed[0]];
            }

            return Arrays::toMap($paramsWithNamedAsArray, function ($paramToNamedMap) {
                return $paramToNamedMap[0];
            }, function ($paramToNamedMap) {
                return $paramToNamedMap[1];
            });
        }

        return [];
    }
}
