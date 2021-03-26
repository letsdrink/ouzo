<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Generator;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\Loader\AnnotationDirectoryLoader;
use Ouzo\Routing\Loader\RouteMetadataCollection;

class RouteFileGenerator
{
    private AnnotationDirectoryLoader $annotationDirectoryLoader;

    #[Inject]
    public function __construct(AnnotationDirectoryLoader $annotationDirectoryLoader)
    {
        $this->annotationDirectoryLoader = $annotationDirectoryLoader;
    }

    public function generate(string $destinationPath, array $resources = []): false|int
    {
        $routesMetadata = $this->annotationDirectoryLoader->load($resources);
        $template = $this->generateFileTemplate($routesMetadata);
        return file_put_contents($destinationPath, $template);
    }

    private function generateFileTemplate(RouteMetadataCollection $routesMetadata): string
    {
        $template = "";
        $template .= "<?php\n\n";
        $template .= "use Ouzo\Routing\Route;\n\n";
        $routesMetadata = $routesMetadata->sort()->toArray();

        foreach ($routesMetadata as $routeMetadata) {
            $responseCode = $routeMetadata->getResponseCode() ? ", ['code' => {$routeMetadata->getResponseCode()}]" : '';

            $template .= sprintf(
                "Route::%s('%s', %s, '%s'%s);\n",
                strtolower($routeMetadata->getHttpMethod()),
                $routeMetadata->getUri(),
                $routeMetadata->getClassNameReference(),
                $routeMetadata->getClassMethod(),
                $responseCode
            );
        }
        return $template;
    }
}
