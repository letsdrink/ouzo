<?php
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\StreamStub;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\DeleteDirectory;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class FilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldDeleteFile()
    {
        //given
        $filePath = Path::joinWithTemp('files_test');
        file_put_contents($filePath, 'test');

        //when
        $isDeleted = Files::delete($filePath);

        //then
        $this->assertTrue($isDeleted);
        $this->assertFileNotExists($filePath);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenNotFoundFileToDelete()
    {
        //given
        $files = new Files();

        //when
        CatchException::when($files)->delete('/broken/path/file');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Utilities\FileNotFoundException');
    }

    /**
     * @test
     */
    public function shouldMoveFile()
    {
        //given
        $filePath = Path::joinWithTemp('files_test');
        file_put_contents($filePath, 'test');
        $newPath = Path::joinWithTemp('new_files_test');

        //when
        $isMoved = Files::move($filePath, $newPath);

        //then
        $this->assertTrue($isMoved);
        $this->assertFileNotExists($filePath);
        $this->assertFileExists($newPath);
        Files::delete($newPath);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenNotFoundSourceFileToMove()
    {
        //given
        $files = new Files();

        //when
        CatchException::when($files)->move('/broken/path/file', '/broken/path/new_file');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Utilities\FileNotFoundException');
    }

    /**
     * @test
     * @dataProvider units
     */
    public function shouldConvertUnits($size, $result)
    {
        //when
        $unit = Files::convertUnitFileSize($size);

        //then
        $this->assertEquals($result, $unit);
    }

    /**
     * @test
     */
    public function shouldReturnWhenZeroPassed()
    {
        //when
        $unit = Files::convertUnitFileSize(0);

        //then
        $this->assertEquals('0 B', $unit);
    }

    /**
     * @test
     */
    public function shouldReturnFileSize()
    {
        //given
        $filePath = Path::joinWithTemp('files_test');
        file_put_contents($filePath, 'test');

        //when
        $size = Files::size($filePath);

        //then
        $this->assertEquals(4, $size);
    }

    /**
     * @test
     */
    public function shouldGetFilesRecursivelyByExtension()
    {
        //given
        mkdir('/tmp/tests_find_files/new_dir/second_new_dir', 0777, true);
        touch('/tmp/tests_find_files/file1a.phtml');
        touch('/tmp/tests_find_files/new_dir/file2a.phtml');
        touch('/tmp/tests_find_files/new_dir/second_new_dir/file3a.phtml');

        //when
        $files = Files::getFilesRecursivelyWithSpecifiedExtension('/tmp/tests_find_files', 'phtml');

        //then
        Assert::thatArray($files)->hasSize(3)
            ->contains(
                '/tmp/tests_find_files/new_dir/second_new_dir/file3a.phtml',
                '/tmp/tests_find_files/new_dir/file2a.phtml',
                '/tmp/tests_find_files/file1a.phtml'
            );
        DeleteDirectory::recursive('/tmp/tests_find_files');
    }

    public function units()
    {
        return array(
            array(10, '10 B'),
            array(143, '143 B'),
            array(10240, '10 KB'),
            array(146432, '143 KB'),
            array(10485760, '10 MB'),
            array(149946368, '143 MB'),
            array(10737418240, '10 GB'),
            array(153545080832, '143 GB'),
        );
    }

    /**
     * @test
     */
    public function shouldCopyFileContent()
    {
        //given
        StreamStub::register('logfile');
        StreamStub::$body = 'content';
        $tmpFileName = Path::joinWithTemp('test' . Clock::nowAsString());

        //when
        Files::copyContent('logfile://input', $tmpFileName);

        //then
        $content = file_get_contents($tmpFileName);

        StreamStub::unregister();
        Files::delete($tmpFileName);

        $this->assertEquals('content', $content);
    }
}