<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\StreamStub;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\DeleteDirectory;
use Ouzo\Utilities\FileNotFoundException;
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
        CatchException::assertThat()->isInstanceOf(FileNotFoundException::class);
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
        CatchException::assertThat()->isInstanceOf(FileNotFoundException::class);
    }

    /**
     * @test
     * @dataProvider units
     * @param int $size
     * @param string $result
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
        $dirPath = Path::joinWithTemp('test', 'tests_find_files', 'new_dir', 'second_new_dir');
        mkdir($dirPath, 0777, true);
        $file1 = Path::joinWithTemp('test', 'tests_find_files', 'file1a.phtml');
        touch($file1);
        $file2 = Path::joinWithTemp('test', 'tests_find_files', 'new_dir', 'file2a.phtml');
        touch($file2);
        $file3 = Path::joinWithTemp('test', 'tests_find_files', 'new_dir', 'second_new_dir', 'file3a.phtml');
        touch($file3);

        //when
        $files = Files::getFilesRecursivelyWithSpecifiedExtension(Path::joinWithTemp('test', 'tests_find_files'), 'phtml');

        //then
        DeleteDirectory::recursive($dirPath);
        Assert::thatArray($files)->hasSize(3)->contains($file1, $file2, $file3);
    }

    public function units()
    {
        return [
            [10, '10 B'],
            [143, '143 B'],
            [10240, '10 KB'],
            [146432, '143 KB'],
            [10485760, '10 MB'],
            [149946368, '143 MB'],
            [10737418240, '10 GB'],
            [153545080832, '143 GB'],
        ];
    }

    /**
     * @test
     */
    public function shouldCopyFileContent()
    {
        //given
        StreamStub::register('logfile');
        StreamStub::$body = 'content';
        $tmpFileName = Path::joinWithTemp('test' . Clock::nowAsString('Y_m_d_H_i_s') . '.txt');

        //when
        Files::copyContent('logfile://input', $tmpFileName);

        //then
        $content = file_get_contents($tmpFileName);

        StreamStub::unregister();
        Files::delete($tmpFileName);

        $this->assertEquals('content', $content);
    }

    /**
     * @test
     */
    public function shouldReturnMimeType()
    {
        //given
        $path = Path::join(ROOT_PATH, 'test', 'resources', 'logo.png');

        //when
        $mimeType = Files::mimeType($path);

        //then
        $this->assertEquals('image/png', $mimeType);
    }

    /**
     * @test
     */
    public function shouldDeleteFileIfExists()
    {
        //given
        $filePath = Path::joinWithTemp('files_test');
        file_put_contents($filePath, 'test');

        //when
        $isDeleted = Files::deleteIfExists($filePath);

        //then
        $this->assertTrue($isDeleted);
        $this->assertFileNotExists($filePath);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfNotExistsAnTryToDelete()
    {
        //when
        $deleteIfExists = Files::deleteIfExists('/broken/path/file');

        //then
        $this->assertFalse($deleteIfExists);
    }
}
