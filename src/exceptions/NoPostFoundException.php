<?php

namespace Hero\Exceptions;

class NoPostFoundException extends Exception{

    public function __construct($message = "Nenhum post foi encontrado", $code = 0) {

        /* Garante que tudo é atribuído corretamente */
        parent::__construct($message, $code);

    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
