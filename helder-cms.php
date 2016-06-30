<?php

require 'vendor/.composer/autoload.php';
require 'Command/DependencyContainer.php';
require 'Command/Install.php';

use Symfony\Component\Console\Shell;
use Symfony\Component\Console\Application;

$application = new Application('HelderCms', '0.0.1-alpha');
$dependencyContainer = new DependencyContainer();
$application->add($dependencyContainer);
$application->add(new Install($dependencyContainer));
$shell = new Shell($application);

$shell->run();
