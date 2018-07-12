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
use Ouzo\Utilities\Functions;
use Throwable;

class FrontController
{
    /** @var string */
    public static $requestId;

    /** @var SessionInitializer */
    private $sessionInitializer;
    /** @var RequestContextFactory */
    private $requestContextFactory;
    /** @var MiddlewareRepository */
    private $middlewareRepository;
    /** @var RequestExecutor */
    private $requestExecutor;
    /** @var array */
    private $defaults;
    /** @var RequestContext */
    private $requestContext;

    /**
     * @Inject
     */
    public function __construct(
        SessionInitializer $sessionInitializer,
        RequestContextFactory $requestContextFactory,
        MiddlewareRepository $middlewareRepository,
        RequestExecutor $requestExecutor
    )
    {
        $this->sessionInitializer = $sessionInitializer;
        $this->requestContextFactory = $requestContextFactory;
        $this->middlewareRepository = $middlewareRepository;
        $this->requestExecutor = $requestExecutor;

        self::$requestId = uniqid();
        $this->defaults = Config::getValue('global');
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

        $this->sessionInitializer->startSession();

        try {
            ob_start();

            //todo remove?
            $afterInitCallback = Config::getValue('callback', 'afterControllerInit');
            if ($afterInitCallback) {
                Functions::call($afterInitCallback, []);
            }

            $chainExecutor->addAll($this->middlewareRepository->getInterceptors());
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

    /** @return SessionInitializer */
    public function getSessionInitializer()
    {
        return $this->sessionInitializer;
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
