<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;

class DownloadHandler
{
    public function downloadFile(array $fileData): void
    {
        header("Content-Type: {$fileData['mime']}");
        header("Content-Disposition: attachment; filename=\"{$fileData['label']}\"");
        $data = Arrays::getValue($fileData, 'data');
        if ($data) {
            $length = strlen($data);
            header("Content-Length:{$length}");
            echo $data;
        } else {
            clearstatcache(true, $fileData['path']);
            $length = filesize($fileData['path']);
            header("Content-Length:{$length}");
            readfile($fileData['path']);
        }
    }

    public function streamMediaFile(array $fileData): void
    {
        $location = $fileData['path'];
        $filename = $fileData['label'];
        $mimeType = Arrays::getValue($fileData, 'mime', 'application/octet-stream');

        clearstatcache(true, $location);

        if (!file_exists($location)) {
            header('HTTP/1.1 404 Not Found');
            return;
        }

        $size = filesize($location);
        $time = date('r', filemtime($location));

        $fm = fopen($location, 'rb');
        if (!$fm) {
            header('HTTP/1.1 505 Internal server error');
            return;
        }

        $begin = 0;
        $end = $size - 1;

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }

        $length = ($end - $begin) + 1;

        header("Content-Type: {$mimeType}");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header("Content-Length:{$length}");
        if (isset($_SERVER['HTTP_RANGE'])) {
            header("Content-Range: bytes {$begin}-{$end}/{$size}");
        }
        header("Content-Disposition: inline; filename={$filename}");
        header('Content-Transfer-Encoding: binary');
        header("Last-Modified: {$time}");

        $cur = $begin;
        fseek($fm, $begin, 0);

        while (!feof($fm) && $cur <= $end && (connection_status() == 0)) {
            print fread($fm, min(1024 * 16, ($end - $cur) + 1));
            $cur += 1024 * 16;
        }
    }
}
