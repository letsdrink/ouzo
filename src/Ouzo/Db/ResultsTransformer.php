<?php

namespace Ouzo\Db;

interface ResultsTransformer
{
    public function transform(array &$results);
}