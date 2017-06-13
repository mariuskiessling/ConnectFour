<?php

namespace ConnectFour\Helpers;

class Logger {
    private $mode;
    private $silent; // print, file

    public function __construct($mode, $silent = false)
    {
        $this->mode = $mode;
        $this->silent = $silent;
    }

    public function log($message) {
        if(!$this->silent && $this->mode == "print")
        {
            printf($message);
        }
    }
}
