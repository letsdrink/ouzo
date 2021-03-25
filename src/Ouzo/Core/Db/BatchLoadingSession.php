<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Closure;
use Exception;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class BatchLoadingSession
{
    private array $queryResultsById = [];
    private static ?BatchLoadingSession $currentSession = null;

    /** @return Model[] */
    public static function getBatch(Model $model): array
    {
        if (self::isAllocated()) {
            return Arrays::getValue(BatchLoadingSession::$currentSession->queryResultsById, spl_object_hash($model), [$model]);
        }
        return [$model];
    }

    public static function isAllocated(): bool
    {
        return BatchLoadingSession::$currentSession !== null;
    }

    public static function allocate(): void
    {
        BatchLoadingSession::$currentSession = new BatchLoadingSession();
    }

    public static function deallocate()
    {
        BatchLoadingSession::$currentSession = null;
    }

    public static function attach(array $results): void
    {
        if (BatchLoadingSession::isAllocated()) {
            foreach ($results as $model) {
                BatchLoadingSession::$currentSession->queryResultsById[spl_object_hash($model)] = $results;
            }
        }
    }

    public static function run(Closure $function): mixed
    {
        $allocatedSession = false;
        try {
            if (!BatchLoadingSession::isAllocated()) {
                BatchLoadingSession::allocate();
                $allocatedSession = true;
            }
            $result = call_user_func($function);
            if ($allocatedSession) {
                BatchLoadingSession::deallocate();
            }
            return $result;
        } catch (Exception $e) {
            if ($allocatedSession) {
                BatchLoadingSession::deallocate();
            }
            throw $e;
        }
    }
}
