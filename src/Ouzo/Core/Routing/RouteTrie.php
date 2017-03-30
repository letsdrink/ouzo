<?php

namespace Ouzo\Routing;


class RouteTrie
{
    private static $trie;

    private static function load()
    {
        if (class_exists('\Helper\CompiledRoutes')) {
            return \Helper\CompiledRoutes::trie();
        }
        return [];
    }

    public static function &trie()
    {
        if (self::$trie === NULL) {
            self::$trie = self::load();
        }
        return self::$trie;
    }

    public static function clear()
    {
        self::$trie = [];
    }
}