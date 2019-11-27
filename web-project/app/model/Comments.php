<?php

namespace App\Model;

use Nette;

class Comments {
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    # xjuric29 methods
    public function getTicket($id) {
        /** Return data for specific ticket. */
        $query = $this->database->table('ticket')->where('id = ?', $id);

        return $query->fetch();
    }
}