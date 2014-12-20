<?php
use Ouzo\Utilities\Path;

class PathTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldJoinPathWithSeparator()
    {
        // given
        $s = DIRECTORY_SEPARATOR;

        // when
        $path = Path::join('/my', 'path', 'to/file.txt');

        // then
        $this->assertEquals("${s}my${s}path${s}to${s}file.txt", $path);
    }

    /**
     * @test
     */
    public function shouldJoinPathWithSeparatorSkippingEmptyElements()
    {
        // given
        $s = DIRECTORY_SEPARATOR;

        // when
        $path = Path::join('my', '', '/file.txt');

        // then
        $this->assertEquals("my${s}file.txt", $path);
    }

    /**
     * @test
     */
    public function shouldJoinPathWithTempDirectory()
    {
        // given
        $s = DIRECTORY_SEPARATOR;
        $tmp = sys_get_temp_dir();

        // when
        $path = Path::joinWithTemp('my/file.txt');

        // then
        $this->assertEquals("${tmp}${s}my${s}file.txt", $path);
    }

    /**
     * @test
     */
    public function shouldRemoveDotsFromRelativePath()
    {
        //given
        $path = 'dir/../dir2/file.txt';

        //when
        $normalized = Path::normalize($path);

        //then
        $this->assertEquals("dir2/file.txt", $normalized);
    }

    /**
     * @test
     */
    public function shouldRemoveDotsFromAbsolutePath()
    {
        //given
        $path = '/tmp/../dir2/file.txt';

        //when
        $normalized = Path::normalize($path);

        //then
        $this->assertEquals("/dir2/file.txt", $normalized);
    }

    /**
     * @test
     */
    public function shouldNotRemoveLeadingDots()
    {
        //given
        $path = '../file.txt';

        //when
        $normalized = Path::normalize($path);

        //then
        $this->assertEquals("../file.txt", $normalized);
    }

    /**
     * @test
     */
    public function shouldRemoveDoubleSlashes()
    {
        //given
        $path = '//dir/file.txt';

        //when
        $normalized = Path::normalize($path);

        //then
        $this->assertEquals("/dir/file.txt", $normalized);
    }
}
