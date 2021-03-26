<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\DownloadHandler;
use Ouzo\Utilities\Arrays;

class MockDownloadHandler extends DownloadHandler
{
    private array $fileData = [];

    public function downloadFile(array $fileData): void
    {
        $this->fileData = $fileData;
    }

    public function streamMediaFile(array $fileData): void
    {
        $this->fileData = $fileData;
    }

    public function getFileName(): ?string
    {
        return Arrays::getValue($this->fileData, 'label');
    }

    public function getFileData(): mixed
    {
        return Arrays::getValue($this->fileData, 'data');
    }
}
