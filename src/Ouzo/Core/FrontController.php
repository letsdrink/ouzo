<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Exception;
use Ouzo\Middleware\MiddlewareRepository;
use Ouzo\Request\RequestContext;
use Ouzo\Request\RequestContextFactory;
use Ouzo\Request\RequestExecutor;
use Ouzo\Utilities\Chain\ChainExecutor;
use Throwable;

class FrontController
{
    /** @var string */
    public static $requestId = null;

    /** @var RequestContextFactory */
    private $requestContextFactory;
    /** @var MiddlewareRepository */
    private $middlewareRepository;
    /** @var RequestExecutor */
    private $requestExecutor;
    /** @var RequestContext */
    private $requestContext;

    /**
     * @Inject
     */
    public function __construct(
        RequestContextFactory $requestContextFactory,
        MiddlewareRepository $middlewareRepository,
        RequestExecutor $requestExecutor
    )
    {
        $this->requestContextFactory = $requestContextFactory;
        $this->middlewareRepository = $middlewareRepository;
        $this->requestExecutor = $requestExecutor;
    }

    /**
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    public function init()
    {
        $this->requestContext = $this->requestContextFactory->create();

        $chainExecutor = new ChainExecutor();
        $chainExecutor->addAll($this->middlewareRepository->getInterceptors());

        try {
            ob_start();

            $chainExecutor->execute($this->requestContext, function (RequestContext $requestContext) {
                $this->requestExecutor->execute($requestContext);
            });
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        ob_end_flush();
    }

    /** @return RequestContext */
    public function getRequestContext()
    {
        return $this->requestContext;
    }

    /** @return RequestExecutor */
    public function getRequestExecutor()
    {
        return $this->requestExecutor;
    }

    /** @return MiddlewareRepository */
    public function getMiddlewareRepository()
    {
        return $this->middlewareRepository;
    }
}
