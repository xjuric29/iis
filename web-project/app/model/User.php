<?php
/**
 * IIS project 2019
 * Description: Class for gaining access to user data.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;
use Tracy\Debugger;

class User {
    private $database;
    private $roleMap = [
        'common_worker' => 'worker',
        'manager' => 'manager',
        'superior' => 'supervisor',
        'administrator' => 'admin'
    ];

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function getUser($login) {
        /**Return one row for user with specified id which is active.
         * @param $login: String with user id. */
        $query = $this->database->table('user')->where('id = ?', $login)->where('deleted = 0');

        return $query->fetch();
    }

    public function getUserType($login) {
        /**Specify if user is customer or worker.
         * @param $login: String with user id.
         * @return: 'customer', 'worker' or null. */
        $type = null;

        $customerQuery = $this->database->table('user_customer')->where('id = ?', $login)->fetch();
        $workerQuery = $this->database->table('user_worker')->where('id = ?', $login)->fetch();

        if ($customerQuery) $type = 'customer';
        if ($workerQuery) $type = 'worker';

        return $type;
    }

    public function getAdditionalUserData($login) {
        /**Return additional user data depending on type of user (customer, worker).
         * @param $login: String with user id. */
        $customerSelect = $this->database->table('user_customer')->where('id = ?', $login)->fetch();
        $workerSelect = $this->database->table('user_worker')->where('id = ?', $login)->fetch();

        if ($customerSelect) return $customerSelect;
        else return $workerSelect;
    }

    public function getArrayForAssigneeSelect() {
        /**Return array of all tickets workers. */
        $selectArray = array();

        $workers = $this->database->table('user_worker')->fetchAll();

        foreach ($workers as $worker) {
            $selectArray[$worker->id] = $worker->ref('user', 'id')->first_name . ' ' .
                $worker->ref('user', 'id')->surname;
        }

        return $selectArray;
    }

    public function convertDbRoleToPretty($dbRole) {
        /**Convert role from DB enum to string which can be display for users of site.
         * @param $dbRole: Raw role string from DB. */
        $role = '';

        if (isset($this->roleMap[$dbRole])) $role = $this->roleMap[$dbRole];

        return $role;
    }
}