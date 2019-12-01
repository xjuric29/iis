<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;


class UserEdit extends MasterPresenter {
    private $user;
    private $database;

    public function __construct(Nette\Database\Context $database, User $user) {
        parent::__construct();
        $this->database = $database;
        $this->user = $user;
    }

    public function getSupervisors() {
        $supervisors = $this->database->table('user_worker')
            ->where('role = superior')
            ->fetchAll();
        $supArray = array();
        foreach($supervisors as $supervisor) {
            $supArray[] = $supervisor['first_name'] . " " . $supervisor['surname'];
        }
        return $supArray;
    }
}
