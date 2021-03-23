<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing\Generator;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Routing\Loader\AnnotationDirectoryLoader;
use Ouzo\Routing\Loader\Loader;
use Ouzo\Routing\Loader\RouteMetadataCollection;

class RouteFileGenerator
{
    /** @var Loader */
    private $loader;

    #[Inject]
    public function __construct(AnnotationDirectoryLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param string $destinationPath
     * @param array $resources
     * @return false|int
     */
    public function generate(string $destinationPath, array $resources = [])
    {
        $routesMetadata = $this->loader->load($resources);
        $template = $this->generateFileTemplate($routesMetadata);
        return file_put_contents($destinationPath, $template);
    }

    /**
     * @param RouteMetadataCollection $routesMetadata
     * @return string
     */
    private function generateFileTemplate(RouteMetadataCollection $routesMetadata)
    {
        $template = "";
        $template .= "<?php\n\n";
        $template .= "use Ouzo\Routing\Route;\n\n";
        $routesMetadata = $routesMetadata->sort()->toArray();

        foreach ($routesMetadata as $routeMetadata) {
            $responseCode = $routeMetadata->getResponseCode() ? sprintf(", ['code' => %s]", $routeMetadata->getResponseCode()) : '';

            $template .= sprintf(
                "Route::%s('%s', %s, '%s'%s);\n",
                strtolower($routeMetadata->getMethod()),
                $routeMetadata->getUri(),
                $routeMetadata->getClassNameReference(),
                $routeMetadata->getClassMethod(),
                $responseCode
            );
        }
        return $template;
    }
}
