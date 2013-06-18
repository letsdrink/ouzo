<?php
namespace Thulium;

class DownloadHandler
{
    public function downloadFile(array $fileData)
    {
        header('Content-Type: ' . $fileData['mime']);
        header('Content-Disposition: attachment; filename="' . $fileData['label'] . '"');
        readfile($fileData['path']);
    }
}