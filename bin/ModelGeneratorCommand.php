<?php
use Ouzo\Tools\Model\Template\ClassPathResolver;
use Ouzo\Tools\Model\Template\Generator;
use Ouzo\Tools\Model\Template\GeneratorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModelGeneratorCommand extends Command
{
    /**
     * @var InputInterface
     */
    private $_input;
    /**
     * @var OutputInterface
     */
    private $_output;

    public function configure()
    {
        $this->setName('ouzo:model_generator')
            ->addOption('table', 't', InputOption::VALUE_REQUIRED)
            ->addOption('class', 'c', InputOption::VALUE_REQUIRED)
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED)
            ->addOption('namespace', 'n', InputOption::VALUE_REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;

        $output->writeln('Model generator');
        if (!$input->getOption('table')) {
            $output->writeln('');
            $output->writeln('Generate model class for specified table.');
            $output->writeln('');
            $output->writeln('parameters: -t=table_name [-n=My\Name\Space] [-c=ClassName] [-f=/path/to/file.php] [-p=prefixToRemove]');
            $output->writeln('');
        }
        $this->_generateModel();
    }

    public function fail($message, $exitCode = 1)
    {
        $this->_output->writeln('ERROR: ' . $message);
        exit($exitCode);
    }

    /**
     * @param Generator $modelGenerator
     * @param $fileName
     * @throws GeneratorException
     */
    private function _saveClassToFile($modelGenerator, $fileName)
    {
        if ($fileName) {
            $this->_output->writeln("Saving class to file: '$fileName'");
            $modelGenerator->saveToFile($fileName);
        }
    }

    private function _generateModel()
    {
        $tableName = $this->_input->getOption('t');
        $className = $this->_input->getOption('c');
        $fileName = $this->_input->getOption('f');
        $nameSpace = $this->_input->getOption('n');
        $tablePrefixToRemove = $this->_input->getOption('p') ? : 't';
        if (empty($tableName))
            $this->fail("Specify table name e.g. -t=users");
        try {
            $modelGenerator = new Generator($tableName, $className, $nameSpace, $tablePrefixToRemove);
            $this->_output->writeln('---------------------------------');
            $this->_output->writeln('Class name: ' . $modelGenerator->getTemplateClassName());
            $this->_output->writeln('---------------------------------');
            $this->_output->writeln($modelGenerator->templateContents());
            $this->_output->writeln('---------------------------------');
            if ($fileName) {
                $this->_saveClassToFile($modelGenerator, $fileName);
            } else {
                $classFileName = ClassPathResolver::forClassAndNamespace($modelGenerator->getTemplateClassName(), $modelGenerator->getClassNamespace())->getClassFileName();
                $this->_saveClassToFile($modelGenerator, $classFileName);
            }
        } catch (GeneratorException $e) {
            $this->fail($e->getMessage());
        }
    }
}