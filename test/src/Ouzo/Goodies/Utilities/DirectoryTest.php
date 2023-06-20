<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\DeleteDirectory;
use Ouzo\Utilities\Directory;
use Ouzo\Utilities\Path;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    private string $directory;

    public function setUp(): void
    {
        $this->directory = Path::joinWithTemp('directory_class');
        mkdir($this->directory, 0777, true);
        file_put_contents(Path::join($this->directory, 'file1.txt'), 'test');
        file_put_contents(Path::join($this->directory, 'file2.txt'), 'some new file');
    }

    public function tearDown(): void
    {
        DeleteDirectory::recursive($this->directory);
    }

    #[Test]
    public function shouldGetDirectorySize()
    {
        //when
        $size = Directory::size($this->directory);

        //then
        $this->assertEquals(17, $size);
    }
}
