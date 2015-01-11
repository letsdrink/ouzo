<?php
namespace Ouzo;

use Ouzo\Utilities\Strings;
use Ouzo\Utilities\TimeAgo;

class TranslatableTimeAgo
{
    /**
     * @var TimeAgo
     */
    private $timeAgo;

    public function __construct(TimeAgo $timeAgo)
    {
        $this->timeAgo = $timeAgo;
    }

    public function asString()
    {
        $key = $this->timeAgo->key;
        $params = $this->timeAgo->params;
        if (Strings::equal($key, 'timeAgo.thisYear')) {
            $params['month'] = I18n::t($params['month']);
        }
        return I18n::t($key, $params);
    }

    public static function create(TimeAgo $timeAgo)
    {
        return new self($timeAgo);
    }
}
