<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Exception;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class BatchLoadingSession
{
    /** @var array */
    private $queryResultsById = [];

    /** @var string|null */
    private static $currentSession;

    /**
     * @param Model $model
     * @return mixed
     */
    public static function getBatch(Model $model)
    {
        if (self::isAllocated()) {
            return Arrays::getValue(BatchLoadingSession::$currentSession->queryResultsById, spl_object_hash($model), [$model]);
        }
        return [$model];
    }

    /**
     * @return bool
     */
    public static function isAllocated()
    {
        return BatchLoadingSession::$currentSession !== NULL;
    }

    /**
     * @return void
     */
    public static function allocate()
    {
        BatchLoadingSession::$currentSession = new BatchLoadingSession();
    }

    /**
     * @return void
     */
    public static function deallocate()
    {
        BatchLoadingSession::$currentSession = null;
    }

    /**
     * @param array $results
     * @return void
     */
    public static function attach(array $results)
    {
        if (BatchLoadingSession::isAllocated()) {
            foreach ($results as $model) {
                BatchLoadingSession::$currentSession->queryResultsById[spl_object_hash($model)] = $results;
            }
        }
    }

    /**
     * @param \Closure $function
     * @throws Exception
     * @return mixed
     */
    public static function run($function)
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
