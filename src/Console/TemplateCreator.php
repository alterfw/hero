<?php
/**
 * Created by PhpStorm.
 * User: sergiovilar
 * Date: 9/7/15
 * Time: 9:48 PM
 */

namespace Hero\Console;
use Symfony\Component\Console\Command\Command;
use Handlebars\Handlebars;

abstract class TemplateCreator extends Command {

  protected function createDirectory($dest) {

    if(!file_exists(APPLICATION_PATH.$dest)) {
      mkdir(APPLICATION_PATH.$dest);
    }

  }

  protected function render($template, $destination, $args) {

    $engine = new Handlebars;
    $template = file_get_contents(__DIR__.'/templates/'.$template.'.template');
    $result = $engine->render($template, $args);
    file_put_contents(APPLICATION_PATH.$destination, $result, FILE_TEXT);

  }

}
