<?php
/**
 * IIS project 2019
 * Description: Class for gaining access to user data.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;

class User {
    private $database;

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
        $customerQuery = $this->database->table('user_customer')->where('id = ?', $login)->fetch();
        $workerQuery = $this->database->table('user_worker')->where('id = ?', $login)->fetch();

        if ($customerQuery) return $customerQuery;
        else return $workerQuery;
    }
}