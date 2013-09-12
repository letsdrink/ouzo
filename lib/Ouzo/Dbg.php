<?php
namespace Ouzo;

class Dbg
{
    static protected $timeFormat = 'Y-m-d H:i:s';

    static public function toWWW()
    {
        $args = func_get_args();

        if (is_array($args) && !empty($args)) {
            $tmpString = '<strong>DEBUG: ' . date(self::$timeFormat) . '</strong><br>';

            foreach ($args as $arg) {
                $tmpString .= highlight_string("<?\n" . print_r($arg, true) . "\n?>", true);
                $tmpString .= '<br /><hr />';
            }

            echo '<div class="debug"
                style="
                    z-index: 1000;
                    border: solid black 2px;
                    background: #FF9999;
                    color: black;
                    font-family: Monospace;
                    max-width: 1200px;
                    word-wrap: break-word;
                ">' . $tmpString . '</div>';
        }
    }
}