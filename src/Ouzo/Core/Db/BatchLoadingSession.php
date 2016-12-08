<?php

namespace Ouzo\Db;


use Exception;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class BatchLoadingSession
{
    private $queryResultsById = array();

    private static $currentSession;

    public static function getBatch(Model $model)
    {
        if (self::isAllocated()) {
            return Arrays::getValue(BatchLoadingSession::$currentSession->queryResultsById, spl_object_hash($model), array($model));
        }
        return array($model);
    }

    public static function isAllocated()
    {
        return BatchLoadingSession::$currentSession !== NULL;
    }

    public static function allocate()
    {
        BatchLoadingSession::$currentSession = new BatchLoadingSession();
    }

    public static function deallocate()
    {
        BatchLoadingSession::$currentSession = null;
    }

    public static function attach(array $results)
    {
        if (BatchLoadingSession::isAllocated()) {
            foreach ($results as $model) {
                BatchLoadingSession::$currentSession->queryResultsById[spl_object_hash($model)] = $results;
            }
        }
    }

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