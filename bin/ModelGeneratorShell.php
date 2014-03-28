<?php
use Ouzo\Shell\InputArgument;
use Ouzo\Shell;
use Ouzo\Tools\Model\Template\Generator;
use Ouzo\Tools\Model\Template\GeneratorException;

class ModelGeneratorShell extends Shell
{
    public function configure()
    {
        $this->addArgument('table', 't', InputArgument::VALUE_REQUIRED);
        $this->addArgument('class', 'c', InputArgument::VALUE_REQUIRED);
        $this->addArgument('file', 'f', InputArgument::VALUE_REQUIRED);
    }

    public function main()
    {
        $this->out('Model generator');
        if (!$this->getArgument('t')) {
            $this->out('');
            $this->out('Generate model class for specified table.');
            $this->out('');
            $this->out('parameters: -t=table_name [-c=ClassName] [-f=/path/to/file.php] [-p=prefixToRemove]');
            $this->out('');
        }
        $this->_generateModel();
    }

    public function fail($message, $exitCode = 1)
    {
        $this->out('ERROR: ' . $message);
        exit($exitCode);
    }

    private function _generateModel()
    {
        $tableName = $this->getArgument('t');
        $className = $this->getArgument('c');
        $fileName = $this->getArgument('f');
        $tablePrefixToRemove = $this->getArgument('p') ? : 't';
        if (empty($tableName))
            $this->fail("Specify table name e.g. -t=users");
        try {
            $modelGenerator = new Generator($tableName, $className, $tablePrefixToRemove);
            $this->out('---------------------------------');
            $this->out('Class name: ' . $modelGenerator->getTemplateClassName());
            $this->out('---------------------------------');
            $this->out($modelGenerator->templateContents());
            $this->out('---------------------------------');
            if ($fileName) {
                $this->out("Saving class to file: '$fileName'");
                $modelGenerator->saveToFile($fileName);
            }
        } catch (GeneratorException $e) {
            $this->fail($e->getMessage());
        }
    }
}