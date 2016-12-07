<?php

namespace Ouzo\Db;


use Exception;
use Ouzo\Model;

class DbSession
{
    private $queryResultsById = array();

    private static $currentSession;

    public static function getModelsFetchTogetherWith(Model $model)
    {
        if (DbSession::$currentSession && isset(DbSession::$currentSession->queryResultsById[$model->getId()])) {
            return DbSession::$currentSession->queryResultsById[$model->getId()];
        }
        return array($model);
    }

    public static function isAllocated()
    {
        return DbSession::$currentSession !== NULL;
    }

    public static function attach(array &$results)
    {
        if (DbSession::$currentSession) {
            foreach ($results as $model) {
                DbSession::$currentSession->queryResultsById[$model->getId()] = $results;
            }
        }
    }

    public static function run($function)
    {
        $allocatedSession = false;
        try {
            if (!DbSession::isAllocated()) {
                DbSession::$currentSession = new DbSession();
                $allocatedSession = true;
            }
            $result = call_user_func($function);
            if ($allocatedSession) {
                DbSession::$currentSession = null;
            }
            return $result;
        } catch (Exception $e) {
            if ($allocatedSession) {
                DbSession::$currentSession = null;
            }
            throw $e;
        }
    }
}