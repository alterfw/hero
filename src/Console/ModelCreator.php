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

class ModelCreator extends TemplateCreator {

  protected function configure() {

    $this
      ->setName('create:model')
      ->setDescription('Create a model class')
      ->addArgument(
        'name',
        InputArgument::REQUIRED,
        'Name of the Model'
      );

  }

  protected function execute(InputInterface $input, OutputInterface $output) {

    $name = ucfirst($input->getArgument('name'));

    $this->createDirectory('/model');
    $this->render('Model', '/model/'.strtolower($name).'.php', ['name'=>$name]);

    $output->writeln('<info>Created</info> '.APPLICATION_PATH.'/model/'.strtolower($name).'.php');

  }

}
