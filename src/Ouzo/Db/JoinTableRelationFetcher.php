<?php

namespace Ouzo\Db;


class JoinTableRelationFetcher implements ResultsTransformer
{
    /**
     * @var JoinTableRelation
     */
    private $_relation;

    function __construct(JoinTableRelation $relation)
    {
        $this->_relation = $relation;
    }

    public function transform(array &$results)
    {
        foreach ($results as $result) {

            $destinationField = $this->_relation->getName();
            $through = $this->_relation->getJoinModelField();
            $result->$destinationField = $this->_relation->extractValue($result->$through);
        }
    }
}