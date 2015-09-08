<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 9/7/15
 * Time: 8:50 PM
 */

namespace Hero\Console;

use Hero\Loader\Seed;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedRunner extends Command {

  protected function configure() {

    $this
      ->setName('db:seed')
      ->setDescription('Run the Seeder class');

  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $loader = new Seed('seed');
    foreach($loader->getInstances() as $instance) {
      $output->writeln("Seeding ".get_class($instance));
      $instance->run();
    }

    $output->writeln("Done.");

  }

}