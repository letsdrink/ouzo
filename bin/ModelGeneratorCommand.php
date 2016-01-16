<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Command;

use Exception;
use Ouzo\AutoloadNamespaces;
use Ouzo\Config;
use Ouzo\Tools\Utils\ClassPathResolver;
use Ouzo\Tools\Model\Template\Generator;
use Ouzo\Tools\Model\Template\GeneratorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModelGeneratorCommand extends Command
{
    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;

    public function configure()
    {
        $defaultNamespace = trim(AutoloadNamespaces::getModelNamespace(), '\\');
        $this->setName('ouzo:model_generator')
            ->addArgument('table', InputArgument::REQUIRED, 'Table name.')
            ->addOption('class', 'c', InputOption::VALUE_REQUIRED, 'Class name. If not specified class name is generated based on table name.')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Class file path. If not specified namespace and class name is used.')
            ->addOption('namespace', 's', InputOption::VALUE_REQUIRED, 'Class namespace (e.g \'Model\MyModel\'). Hint: Remember to escape backslash (\\\\)!', $defaultNamespace)
            ->addOption('remove-prefix', 'p', InputOption::VALUE_REQUIRED, 'Remove prefix from table name when generating class name.', 't')
            ->addOption('output-only', 'o', InputOption::VALUE_NONE, 'Only displaying generated model class.')
            ->addOption('short-arrays', 'a', InputOption::VALUE_NONE, 'Generate model class with short arrays. (PHP 5.4).');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->generateModel();
    }

    public function fail($message, $exitCode = 1)
    {
        throw new Exception($message, $exitCode);
    }

    private function generateModel()
    {
        $tableName = $this->input->getArgument('table');
        $className = $this->input->getOption('class');
        $fileName = $this->input->getOption('file');
        $nameSpace = $this->input->getOption('namespace');
        $tablePrefixToRemove = $this->input->getOption('remove-prefix') ?: 't';
        $shortArrays = $this->input->getOption('short-arrays');
        if (empty($tableName)) {
            $this->fail("Specify table name e.g. users");
        }
        try {
            $modelGenerator = new Generator($tableName, $className, $nameSpace, $tablePrefixToRemove, $shortArrays);
            $this->output->writeln('---------------------------------');
            $this->writeInfo('Database name: <info>%s</info>', Config::getValue('db', 'dbname'));
            $this->writeInfo('Class name: <info>%s</info>', $modelGenerator->getTemplateClassName());
            $this->writeInfo('Class namespace: <info>%s</info>', $modelGenerator->getClassNamespace());
            $this->output->writeln('---------------------------------');
            $this->output->writeln($modelGenerator->templateContents());
            $this->output->writeln('---------------------------------');
            if ($fileName) {
                $this->saveClassToFile($modelGenerator, $fileName);
            } else {
                $classFileName = ClassPathResolver::forClassAndNamespace($modelGenerator->getTemplateClassName(), $modelGenerator->getClassNamespace())->getClassFileName();
                $this->saveClassToFile($modelGenerator, $classFileName);
            }
        } catch (GeneratorException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @param Generator $modelGenerator
     * @param $fileName
     * @throws GeneratorException
     */
    private function saveClassToFile($modelGenerator, $fileName)
    {
        $outputOnly = $this->input->getOption('output-only');
        if ($fileName && !$outputOnly) {
            $this->output->writeln("Saving class to file: '$fileName'");
            $modelGenerator->saveToFile($fileName);
        }
    }

    private function writeInfo($info, $value)
    {
        $this->output->writeln(sprintf($info, $value));
    }
}
