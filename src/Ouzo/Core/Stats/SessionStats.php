<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
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
    public const NUMBER_OF_REQUESTS_TO_KEEP = 10;
    private Uri $uri;

    #[Inject]
    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    public function save(RequestContext $requestContext): void
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

    private function removeExcessiveRequests(): void
    {
        $all = $this->queries();
        if (sizeof($all) > self::NUMBER_OF_REQUESTS_TO_KEEP) {
            while (sizeof($all) > self::NUMBER_OF_REQUESTS_TO_KEEP) {
                array_shift($all);
            }
            Session::set('stats_queries', $all);
        }
    }

    public function queries(): array
    {
        return Session::get('stats_queries') ?: [];
    }

    public function reset(): void
    {
        Session::remove('stats_queries');
    }

    public function getTotalTime(): int
    {
        $totalTime = 0;

        foreach (array_keys($this->queries()) as $request) {
            $totalTime += $this->getRequestTotalTime($request);
        }

        return $totalTime;
    }

    public function getRequestTotalTime(?string $request): mixed
    {
        return array_reduce($this->queriesForRequest($request), function ($total, $value) {
            return $total + $value['time'];
        });
    }

    public function getNumberOfQueries(): int
    {
        $number = 0;

        foreach (array_keys($this->queries()) as $request) {
            $number += $this->getRequestNumberOfQueries($request);
        }

        return $number;
    }

    public function getRequestNumberOfQueries(?string $request): int
    {
        return sizeof($this->queriesForRequest($request));
    }

    public function getNumberOfRequests(): int
    {
        return sizeof($this->queries());
    }

    private function queriesForRequest(?string $request): array
    {
        return Session::get('stats_queries', $request, 'queries') ?: [];
    }
}
