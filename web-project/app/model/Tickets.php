<?php

namespace App\Model;

use Nette;

class Tickets {
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function getTicketTable($orderBy, $order) {

    }

}