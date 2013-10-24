<?php

namespace ModelGenerator;
use Ouzo\Shell;
use Ouzo\Shell\InputArgument;


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
            $modelGenerator = new ModelGenerator($tableName, $className);
            $this->out($modelGenerator->getClassContents());
            $modelGenerator->flushToFile($fileName);
        } catch (ModelGeneratorException $e) {
            $this->fail($e->getMessage());
        }
    }

}
