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

    public function getRoleArr($userId) {
        $roles = [
            "common_worker" => "Employee",
            "superior" => "Supervisor",
            "manager" => "Manager",
            "administrator" => "Administrator",
        ];
        $curRole = $this->database->query('
            SELECT role
            FROM user_worker
            WHERE id LIKE ?;
            ', $userId)
            ->fetch();
        if($curRole != null) {
            $curRole = $curRole['role'];
            $roleArray = [$curRole => $roles["$curRole"]];
            unset($roles["$curRole"]);
            $roleArray += $roles;

            return $roleArray;
        }
        return ["customer" => "Customer"];
    }

    public function getSupervisorArr($userId) {
        $curSupervisor = $this->database->query('
            SELECT user.id, user.first_name, user.surname
            FROM user
            WHERE user.id LIKE (SELECT user_worker.superior FROM user_worker WHERE user_worker.id = ?)
            ', $userId)
            ->fetch();
        if($curSupervisor == null) {
            $supArray = ["none" => "None"];
            $curSupervisor = "";
        }
        else {
            $supArray = [
                $curSupervisor['id'] => ($curSupervisor['first_name'] . " " . $curSupervisor['surname']),
                "none" => "None"
            ];
            $curSupervisor = $curSupervisor['id'];
        }

        $supervisors = $this->database->query('
            SELECT user.id, user.first_name, user.surname, user_worker.role
            FROM user_worker
            NATURAL JOIN user
            WHERE (user_worker.role = "superior" OR user_worker.role = "manager") AND user.id NOT LIKE ? AND user.id NOT LIKE ?
            ', $userId, $curSupervisor)
            ->fetchAll();

        foreach($supervisors as $supervisor) {
            $supArray += [$supervisor['id'] => $supervisor['first_name'] . " " . $supervisor['surname']];
        }
        return $supArray;
    }

    public function editUser($userId, $values) {
        $this->database->table('user')
            ->where('id', $userId)
            ->update([
                'first_name' => $values->fname,
                'surname' => $values->sname,
                'mail' => $values->mail,
            ]);
        if ($this->user->getUserType($userId) == "worker") {
            if ($values->supSelect != "none") {
                $this->database->table('user_worker')
                    ->where('id', $userId)
                    ->update([
                        'role' => $values->roleSelect,
                        'superior' => $values->supSelect,
                    ]);
            }
            else {
                $this->database->table('user_worker')
                    ->where('id', $userId)
                    ->update([
                        'role' => $values->roleSelect,
                        'superior' => NULL,
                    ]);
            }
        }
        else {
            $this->database->table('user_customer')
                ->where('id', $userId)
                ->update([
                    'company' => $values->comp,
                ]);
        }

    }
}
