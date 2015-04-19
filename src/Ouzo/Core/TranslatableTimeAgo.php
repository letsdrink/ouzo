<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Strings;
use Ouzo\Utilities\TimeAgo;

class TranslatableTimeAgo
{
    /**
     * @var TimeAgo
     */
    private $timeAgo;

    private function __construct(TimeAgo $timeAgo)
    {
        $this->timeAgo = $timeAgo;
    }

    public function asString()
    {
        $key = $this->timeAgo->getKey();
        $params = $this->timeAgo->getParams();
        if (Strings::equal($key, 'timeAgo.thisYear')) {
            $params['month'] = I18n::t($params['month']);
        }
        return I18n::t($key, $params);
    }

    public static function create($date)
    {
        $timeAgo = new TimeAgo($date);
        return new TranslatableTimeAgo($timeAgo);
    }
}
