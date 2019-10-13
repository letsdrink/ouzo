<?php
use Ouzo\Utilities\DeleteDirectory;
use Ouzo\Utilities\Directory;
use Ouzo\Utilities\Path;

use PHPUnit\Framework\TestCase; 

class DirectoryTest extends TestCase
{
    private $directory;

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

    /**
     * @test
     */
    public function shouldGetDirectorySize()
    {
        //when
        $size = Directory::size($this->directory);

        //then
        $this->assertEquals(17, $size);
    }
}
