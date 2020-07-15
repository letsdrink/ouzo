<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Stats;

use Ouzo\Config;
use Ouzo\Db\Stats;
use Ouzo\FrontController;
use Ouzo\Injection\Annotation\Inject;
use Ouzo\Request\RequestContext;
use Ouzo\Session;
use Ouzo\Uri;

class SessionStats
{
    const NUMBER_OF_REQUESTS_TO_KEEP = 10;

    /** @var Uri */
    private $uri;

    /**
     * @Inject
     */
    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param RequestContext $requestContext
     * @return void
     */
    public function save(RequestContext $requestContext)
    {
        $traceDisabled = !(Config::getValue('debug') && Config::getValue('stats_disabled') !== true);
        if ($traceDisabled) {
            return;
        }

        $requestDetails = $this->uri->getPathWithoutPrefix() . '#' . FrontController::$requestId;
        $controller = $requestContext->getCurrentControllerObject();

        Session::push('stats_queries', $requestDetails, 'request_params', $controller->params);
        foreach (Stats::$queries as $query) {
            Session::push('stats_queries', $requestDetails, 'queries', $query);
        }

        Stats::reset();

        $this->removeExcessiveRequests();
    }

    /**
     * @return void
     */
    private function removeExcessiveRequests()
    {
        $all = $this->queries();
        if (sizeof($all) > self::NUMBER_OF_REQUESTS_TO_KEEP) {
            while (sizeof($all) > self::NUMBER_OF_REQUESTS_TO_KEEP) {
                array_shift($all);
            }
            Session::set('stats_queries', $all);
        }
    }

    /**
     * @return array
     */
    public function queries()
    {
        return Session::get('stats_queries') ?: [];
    }

    /**
     * @return void
     */
    public function reset()
    {
        Session::remove('stats_queries');
    }

    /**
     * @return int
     */
    public function getTotalTime()
    {
        $totalTime = 0;

        foreach (array_keys($this->queries()) as $request) {
            $totalTime += $this->getRequestTotalTime($request);
        }

        return $totalTime;
    }

    /**
     * @param string $request
     * @return mixed
     */
    public function getRequestTotalTime($request)
    {
        return array_reduce($this->queriesForRequest($request), function ($total, $value) {
            return $total + $value['time'];
        });
    }

    /**
     * @return int
     */
    public function getNumberOfQueries()
    {
        $number = 0;

        foreach (array_keys($this->queries()) as $request) {
            $number += $this->getRequestNumberOfQueries($request);
        }

        return $number;
    }

    /**
     * @param string $request
     * @return int
     */
    public function getRequestNumberOfQueries($request)
    {
        return sizeof($this->queriesForRequest($request));
    }

    /**
     * @return int
     */
    public function getNumberOfRequests()
    {
        return sizeof($this->queries());
    }

    /**
     * @param string $request
     * @return array
     */
    private function queriesForRequest($request)
    {
        return Session::get('stats_queries', $request, 'queries') ?: [];
    }
}
