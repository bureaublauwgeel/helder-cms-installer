<?php

/**
 * This file is part of the helder cms installer from Bureau Blauwgeel.
 *
 * (c) 2016 Ray Kootstra <r.kootstra@bureaublauwgeel.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require 'vendor/autoload.php';
require 'Command/DependencyContainer.php';
require 'Command/Install.php';

use Symfony\Component\Console\Shell;
use Symfony\Component\Console\Application;
use Command\DependencyContainer;
use Command\Install;

$application = new Application('HelderCms', '0.0.1-alpha');
$dependencyContainer = new DependencyContainer();
$application->add($dependencyContainer);
$application->add(new Install($dependencyContainer));
$shell = new Shell($application);

$shell->run();
