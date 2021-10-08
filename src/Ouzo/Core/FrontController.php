<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Middleware\MiddlewareRepository;
use Ouzo\Request\RequestContext;
use Ouzo\Request\RequestContextFactory;
use Ouzo\Request\RequestExecutor;
use Ouzo\Stats\SessionStats;
use Ouzo\Utilities\Chain\ChainExecutor;
use Throwable;

class FrontController
{
    public static ?string $requestId = null;

    private ?RequestContext $requestContext = null;

    #[Inject]
    public function __construct(
        private RequestContextFactory $requestContextFactory,
        private MiddlewareRepository $middlewareRepository,
        private RequestExecutor $requestExecutor,
        private SessionStats $sessionStats
    )
    {
    }

    public function init(): void
    {
        $this->requestContext = $this->requestContextFactory->create();

        $chainExecutor = new ChainExecutor();
        $chainExecutor->addAll($this->middlewareRepository->getInterceptors());

        try {
            ob_start();

            $chainExecutor->execute($this->requestContext, function (RequestContext $requestContext) {
                $this->requestExecutor->execute($requestContext);
            });
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        } finally {
            $this->sessionStats->save($this->requestContext);
        }

        if (ob_get_length() !== false) {
            ob_end_flush();
        }
    }

    public function getRequestContext(): RequestContext
    {
        return $this->requestContext;
    }

    public function getRequestExecutor(): RequestExecutor
    {
        return $this->requestExecutor;
    }

    public function getMiddlewareRepository(): MiddlewareRepository
    {
        return $this->middlewareRepository;
    }
}
