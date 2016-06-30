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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Process\Process;

/**
 * Class Install
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Install extends Command
{
    /** @var String */
    protected $installDir;

    /** @var String */
    protected $failingProcess;

    /** @var DependencyContainer */
    protected $dependenciesContainer;

    /**
     * Install constructor.
     *
     * @param DependencyContainer $dependenciesContainer
     */
    public function __construct(DependencyContainer $dependenciesContainer)
    {
        parent::__construct();

        $this->dependenciesContainer = $dependenciesContainer;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->createInstallationDirectory($output)
            && $this->downloadComposer($output)
            && $this->generateJson($output)
            && $this->installer($output)
        ) {
            $output->writeln('<info>Helder CMS is installed!</info>');
        } else {
            $output->writeln('<error>An error occurred while trying to install the Helder CMS: </error>');

            if ($this->failingProcess instanceof Process) {
                $output->writeln('<error>%s</error>', $this->failingProcess->getErrorOutput());
            }
        }
    }

    protected function configure()
    {
        $this
            ->setName('install');
    }

    /**
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function createInstallationDirectory(OutputInterface $output)
    {
        /** @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');
        $this->installDir = $dialog->ask(
            $output,
            '<question>Please specify a non-existing directory to start the Helder CMS installation: </question>'
        );

        $mkdir = null;

        if (!is_dir($this->installDir)) {
            $mkdir = new Process(sprintf('mkdir -p %s', $this->installDir));
            $mkdir->run();

            if ($mkdir->isSuccessful()) {
                $output->writeln(sprintf('<info>Directory %s succesfully  created</info>', $this->installDir));

                return true;
            }
        }

        $this->failingProcess = $mkdir;

        return false;
    }

    /**
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function downloadComposer(OutputInterface $output)
    {
        $wget = new Process(
            sprintf('wget getcomposer.org/composer.phar -O %s/composer.phar', $this->installDir, $this->installDir)
        );
        $wget->run();

        if ($wget->isSuccessful()) {
            $output->writeln('<info>Downloaded composer in the installation directory</info>');

            return true;
        }

        $this->failingProcess = $wget;

        return false;
    }

    /**
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function generateJson(OutputInterface $output)
    {
        $skeleton = file_get_contents(__DIR__ . "/../composer.s");
        $dependencies = implode(',', $this->dependenciesContainer->getDependencies());
        $skeleton = str_replace('PLACEHOLDER', $dependencies, $skeleton);

        if (file_put_contents($this->installDir . "/composer.json", $skeleton)) {
            $output->writeln('<info>composer.json has been created</info>');

            return true;
        }

        return false;
    }

    /**
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function installer(OutputInterface $output)
    {
        $install = new Process(sprintf('cd %s && php composer.phar install', $this->installDir));
        $install->run();

        if ($install->isSuccessful()) {
            $output->writeln('<info>Packages succesfully installed</info>');

            return true;
        }

        $this->failingProcess = $install;

        return false;
    }
}
