<?php

namespace App\Model;

use Nette;

class PermissionMap {
    /**Our own easier solution for permission management in this project. Name of role will be converted to number by
     * 'level of access' (higher number means more permissions). This lead to easier comparison. Converted number will
     * be stored in template variable 'userInfo' in startup method of MasterPresenter.
     */
    private $permissions = [
        'customer' => 1,
        'common_worker' => 2,
        'manager' => 3,
        'superior' => 4,
        'administrator' => 5
    ];

    public function convert($role) {
        /**Convert role string to number. If role doesn't match with predefined values, 0 will be returned.
         * @param $role: string with name of role. */
        $roleNumber = 0;

        if (isset($this->permissions[$role])) $roleNumber = $this->permissions[$role];

        return $roleNumber;
    }
}