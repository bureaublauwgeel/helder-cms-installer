<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DependencyContainer
 */
class DependencyContainer extends Command
{
    /** @var array */
    protected $dependencies = array();

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $package = $dialog->ask(
            $output,
            '<question>Please enter the name of the package you want to install</question>'
        );

        if ($package) {
            $this->dependencies[] = '"' . $package . '": "master"';
            $output->writeln(sprintf('<info>Package %s was succesfully registered</info>', $package));
        } else {
            $output->writeln('<error>You must insert a package name</error>');
        }
    }

    protected function configure()
    {
        $this
            ->setName('add-dependency');;
    }
}
