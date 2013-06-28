<?php
use Thulium\Shell;
use Thulium\Shell\Dispatcher;
use Thulium\Shell\InputArgument;
use Thulium\Tests\MockShellOutput;

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
}