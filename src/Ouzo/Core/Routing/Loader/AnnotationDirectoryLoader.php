<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Loader;

use FilesystemIterator;
use InvalidArgumentException;
use Ouzo\Injection\Annotation\Inject;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;

class AnnotationDirectoryLoader implements Loader
{
    private AnnotationClassLoader $annotationClassLoader;

    #[Inject]
    public function __construct(AnnotationClassLoader $annotationClassLoader)
    {
        $this->annotationClassLoader = $annotationClassLoader;
    }

    public function load(array $paths): RouteMetadataCollection
    {
        $collection = new RouteMetadataCollection();
        $files = $this->resolveFiles($this->resolvePaths($paths));
        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $reflectionClass = new ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();

            if (in_array($sourceFile, $files, true)) {
                $collection->addCollection($this->annotationClassLoader->load([$className]));
            }
        }

        return $collection;
    }

    /**
     * @param string[] $paths
     * @return string[]
     */
    public function resolvePaths(array $paths): array
    {
        $resolvedPaths = [];

        foreach ($paths as $path) {
            if (is_dir($path)) {
                $resolvedPaths[] = $path;
            } elseif ($glob = glob($path, GLOB_ONLYDIR | GLOB_NOSORT)) {
                sort($glob);
                $resolvedPaths = array_merge($resolvedPaths, $glob);
            } else {
                throw new InvalidArgumentException(sprintf('Directory "%s" does not exist.', $path));
            }
        }
        return $resolvedPaths;
    }

    /**
     * @param string[] $paths
     * @return string[]
     */
    private function resolveFiles(array $paths): array
    {
        $includedFiles = [];
        foreach ($paths as $path) {
            $files = new RegexIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+' . preg_quote('.php') . '$/i',
                RecursiveRegexIterator::GET_MATCH
            );

            foreach ($files as $file) {
                $sourceFile = realpath($file[0]);
                require_once $sourceFile;
                $includedFiles[] = $sourceFile;
            }
        }
        return $includedFiles;
    }
}
