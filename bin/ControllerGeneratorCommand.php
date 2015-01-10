<?php
namespace Command;

use Ouzo\Tools\Controller\Template\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerGeneratorCommand extends Command
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
        $this->setName('ouzo:controller_generator')
            ->addArgument('controller', InputArgument::REQUIRED, 'Controller name')
            ->addArgument('action', InputArgument::OPTIONAL, 'Action name');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->generateController();
    }

    private function generateController()
    {
        $controllerName = $this->input->getArgument('controller');
        $generator = new Generator($controllerName);
        $this->output->writeln('---------------------------------');
        $this->output->writeln('Class name: <info>' . $generator->getClassName() . '</info>');
        $this->output->writeln('Class namespace: <info>' . $generator->getClassNamespace() . '</info>');
        $this->output->writeln('---------------------------------');
        if (!$generator->isControllerExists()) {

        }
    }
}
