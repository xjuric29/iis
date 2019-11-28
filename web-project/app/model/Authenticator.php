<?php
/**
 * IIS project 2019
 * Description: Authenticator class for registered user check.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette\Security as NS;


class Authenticator implements NS\IAuthenticator{
    private $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    function authenticate(array $credentials): NS\IIdentity {
        list($login, $password) = $credentials;

        // Get general user info.
        $user = $this->user->getUser($login);

        # Credentials checking.
        if (!$user) throw new NS\AuthenticationException('User not found.');
        if (!password_verify($password, $user->hash)) throw new NS\AuthenticationException('Invalid password.');

        // Create identity with general user information.
        $identity = new NS\Identity($user->id, null, [
            'firstName' => $user->first_name,
            'surname' => $user->surname,
            'mail' => $user->mail
        ]);

        // If user is customer.
        if ($this->user->getUserType($login) == 'customer') $identity->setRoles(['customer']);
        // Else if it is worker.
        else {
            // Get specific user info for workers including role.
            $worker = $this->user->getAdditionalUserData($login);

            $identity->setRoles([$worker->role]);
        }

        return $identity;
    }
}