<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;


class UserDetail extends MasterPresenter {
    private $user;
    private $database;

    public function __construct(Nette\Database\Context $database, User $user) {
        parent::__construct();
        $this->database = $database;
        $this->user = $user;
    }

    public function getUserData($userId) {
        if ($this->user->getUserType($userId) == "worker") {
            $retval = $this->database->query('
                SELECT user_worker.id, user_worker.role, user_worker.superior, user.first_name,
                user.surname, user.mail, user.deleted
                FROM user_worker
                NATURAL JOIN user
                WHERE user.id = ?',
                $userId
            )->fetch();
        }
        else {
            $retval = $this->database->query('
                SELECT user_customer.id, user_customer.company, user.first_name, user.surname, user.mail, user.deleted
                FROM user_customer
                NATURAL JOIN user
                WHERE user.id = ?',
                $userId
            )->fetch();
        }
        return $retval;
    }

    public function getUserType($userId) {
        return $this->user->getUserType($userId);
    }

    public function getSupervisor($userId) {
        if ($this->getUserType($userId) == "worker" && $this->getUserData($userId)['superior']) {
            $superior = $this->database->table('user')
                ->where('id LIKE ?', $this->getUserData($userId)['superior'])
                ->fetch();
            return $superior['first_name'] . " " . $superior['surname'];
        }
        return "";
    }

    public function isUser($userId) {
        return $this->database->query('SELECT id FROM user WHERE id LIKE ?', $userId)
            ->fetch();
    }
}
