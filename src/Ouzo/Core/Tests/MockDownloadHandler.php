<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tests;

class MockDownloadHandler
{
    private $_fileName;

    public function downloadFile($fileData)
    {
        $this->_fileName = $fileData['label'];
        return $this;
    }

    public function streamMediaFile(array $fileData)
    {
        $this->_fileName = $fileData['label'];
        return $this;
    }

    public function getFileName()
    {
        return $this->_fileName;
    }
}
