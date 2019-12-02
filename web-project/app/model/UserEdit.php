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

    public function getSupervisorArr($userId)
    {
        if ($userId) {
            $curSupervisor = $this->database->query('
            SELECT user.id, user.first_name, user.surname
            FROM user
            WHERE user.id LIKE (SELECT user_worker.superior FROM user_worker WHERE user_worker.id = ?)
            ', $userId)
                ->fetch();

            if ($curSupervisor == null) {
                $supArray = ["none" => "None"];
                $curSupervisor = "";
            } else {
                $supArray = [
                    $curSupervisor['id'] => ($curSupervisor['first_name'] . " " . $curSupervisor['surname']),
                    "none" => "None"
                ];
                $curSupervisor = $curSupervisor['id'];
            }
        }
        else {
            $userId = "";
            $curSupervisor = "";
            $supArray = ["none" => "None"];
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

    public function editUser($userId, $values, $form) {
        $this->database->table('user')
            ->where('id', $userId)
            ->update([
                'first_name' => $values->fname,
                'surname' => $values->sname,
                'mail' => $values->mail,
            ]);

        if ($this->user->getUserType($userId) == "worker") {
            // Property_exists checks if the superior and role can be updated - users cannot update these, only admins can
            if (property_exists($values, 'supSelect')) {
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
        }
        else {
            $this->database->table('user_customer')
                ->where('id', $userId)
                ->update([
                    'company' => $values->company,
                ]);
        }
        // Update password if requested, check that confirmation matches
        if($values->pwd) {
            if($values->pwd == $values->pwdconf) {
                $this->database->table('user')
                    ->where('id', $userId)
                    ->update([
                        'hash' => password_hash($values->pwd, PASSWORD_BCRYPT),
                    ]);
            }
            else {
                return false;
            }
        }
        return true;
    }

    public function deleteUser($userId) {
        $this->database->table('user')
            ->where('id', $userId)
            ->update([
                'deleted' => 1,
            ]);
    }

    public function newEmployee($values, $form) {
        try {
            $this->database->table('user')
                ->insert([
                    'id' => $values->login,
                    'first_name' => $values->fname,
                    'surname' => $values->sname,
                    'mail' => $values->mail,
                    'hash' => password_hash($values->pwd, PASSWORD_BCRYPT),
                ]);

            if ($values->supSelect != "none") {
                $this->database->table('user_worker')
                    ->insert([
                        'id' => $values->login,
                        'role' => $values->roleSelect,
                        'superior' => $values->supSelect,
                    ]);
            }
            else {
                $this->database->table('user_worker')
                    ->insert([
                        'id' => $values->login,
                        'role' => $values->roleSelect,
                        'superior' => NULL,
                    ]);
            }
        }
        catch (\Nette\Database\UniqueConstraintViolationException $e) {
            return false;
        }
        return true;
    }

    public function newCustomer($values) {
        try {
            $this->database->table('user')
                ->insert([
                    'id' => $values->login,
                    'first_name' => $values->fname,
                    'surname' => $values->sname,
                    'mail' => $values->mail,
                    'hash' => password_hash($values->pwd, PASSWORD_BCRYPT),
                ]);

            $this->database->table('user_customer')
                ->insert([
                    'id' => $values->login,
                    'company' => $values->company,
                ]);
        }
        catch (\Nette\Database\UniqueConstraintViolationException $e) {
            return false;
        }
        return true;
    }
}
