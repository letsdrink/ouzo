<?php
use Ouzo\Shell\Dispatcher;
use Ouzo\Shell\InputArgument;
use Ouzo\Shell;
use Ouzo\Tests\MockShellOutput;

class SampleShell extends Shell
{
    public function __construct()
    {
        $this->stdout = new MockShellOutput();
    }

    public function configure()
    {
        $this->addArgument('test', 't', InputArgument::VALUE_OPTIONAL);
    }

    public function main()
    {
        $this->out('SAMPLE CONSOLE');
        $this->out('ARG:' . $this->getArgument('test'));
    }
}

class SampleClassButNotExtendsShell
{

}

class DispatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldWorkWhenArgsNotPassed()
    {
        //given
        $argv = array('shell.php', 'Sample');

        //when
        Dispatcher::runScript($argv);

        //then
        $this->expectOutputRegex('/ARG:/');
    }

    /**
     * @test
     */
    public function shouldWorkWhenArgsPassed()
    {
        //given
        $argv = array('shell.php', 'Sample', '--test=John');

        //when
        Dispatcher::runScript($argv);

        //then
        $this->expectOutputRegex('/ARG:John/');
    }

    /**
     * @test
     */
    public function shouldEchoMessageFromUserException()
    {
        //given
        $argv = array('shell.php');

        //when
        Dispatcher::runScript($argv);

        //then
        $this->expectOutputRegex('/Error: Empty application name./');
    }

    /**
     * @test
     */
    public function shouldEchoErrorIfApplicationNotFoundOr()
    {
        //given
        $argv = array('shell.php', 'NotExist');

        //when
        Dispatcher::runScript($argv);

        //then
        $this->expectOutputRegex("/Class 'NotExistShell' for application 'NotExist' does not exist./");
    }

    /**
     * @test
     */
    public function shouldEchoErrorIfApplicationNotShell()
    {
        //given
        $argv = array('shell.php', 'SampleClassButNotExtends');

        //when
        Dispatcher::runScript($argv);

        //then
        $this->expectOutputRegex("/Error: Class 'SampleClassButNotExtendsShell' is not shell application./");
    }

    public function _invokeDispatcher($argv)
    {
        Dispatcher::runScript($argv);
    }
}