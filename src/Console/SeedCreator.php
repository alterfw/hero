<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 9/7/15
 * Time: 9:48 PM
 */

namespace Hero\Console;


use Hero\Console\TemplateCreator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCreator extends TemplateCreator {

  protected function configure() {

    $this
      ->setName('create:seed')
      ->setDescription('Create a seed class')
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of the Model'
      );

  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $name = ucfirst($input->getArgument('name'));

    $this->createDirectory('/seed');
    $this->render('Seed', '/seed/'.$name.'Seeder.php', ['name'=>$name]);

    $output->writeln('<info>Created</info> '.APPLICATION_PATH.'/seed/'.$name.'Seeder.php');

  }

}