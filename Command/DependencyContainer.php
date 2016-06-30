<?php

namespace Command;

/**
 * This file is part of the helder cms installer from Bureau Blauwgeel.
 *
 * (c) 2016 Ray Kootstra <r.kootstra@bureaublauwgeel.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DependencyContainer
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
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
        $this->setName('add-dependency');
    }
}
