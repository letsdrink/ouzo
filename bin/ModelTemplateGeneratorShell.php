<?php

use Ouzo\Shell;
use Ouzo\Shell\InputArgument;
use Ouzo\Tools\Model\Template\Generator;
use Ouzo\Tools\Model\Template\GeneratorException;


class ModelTemplateGeneratorShell extends Shell
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
        $this->out('');
        $this->out('Generate model class template for specified table.');
        $this->out('');
        $this->out('parameters: -t=table_name [-c=ClassName] [-f=/path/to/file.php]');
        $this->out('');
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
        if (empty($tableName))
            $this->fail("Specify table name e.g. -t=t_user");
        try {
            $modelGenerator = new Generator($tableName, $className);
            $this->out($modelGenerator->getTemplateClassName());
            if ($fileName)
                $modelGenerator->saveToFile($fileName);
        } catch (GeneratorException $e) {
            $this->fail($e->getMessage());
        }
    }

}
