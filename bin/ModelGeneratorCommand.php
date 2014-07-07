<?php
use Ouzo\Config;
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
            ->addOption('table', 't', InputOption::VALUE_REQUIRED, 'Table name')
            ->addOption('class', 'c', InputOption::VALUE_REQUIRED, 'Class name. If not specified class name is generated base on table name')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Class file path. If not specified namespace and class name is used')
            ->addOption('namespace', 's', InputOption::VALUE_REQUIRED, 'Class namespace. Default namespace is Model.', 'Model')
            ->addOption('remove_prefix', 'p', InputOption::VALUE_REQUIRED, 'Remove prefix from table name when generating class name.', 't');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_input = $input;
        $this->_output = $output;
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
        $tableName = $this->_input->getOption('table');
        $className = $this->_input->getOption('class');
        $fileName = $this->_input->getOption('file');
        $nameSpace = $this->_input->getOption('namespace');
        $tablePrefixToRemove = $this->_input->getOption('remove_prefix') ? : 't';
        if (empty($tableName))
            $this->fail("Specify table name e.g. -t users");
        try {
            $modelGenerator = new Generator($tableName, $className, $nameSpace, $tablePrefixToRemove);
            $this->_output->writeln('---------------------------------');
            $this->_output->writeln('Database name: ' . Config::getValue('db', 'dbname'));
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