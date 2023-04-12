<?php
class Handshake {
    public function __construct() {
        $this->name = 'Uas';
    }

    public function hello() {
        echo "Welcome to {$this->name}!";
    }
}