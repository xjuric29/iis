<?php
/**
 * IIS project 2019
 * Description: Presenter for displaying ticket detail and its actions.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;
use Nette\Application\UI\Form;

class MasterPresenter extends Nette\Application\UI\Presenter {
    /** @var \App\Model\PermissionMap @inject */
    public $permissionMap;
    protected $userInfo;

    public function startup() {
        parent::startup();

        // Get object with user information.
        $identity = $this->getUser()->getIdentity();

        // If user is logged.
        if ($identity) {
            // Convert role to number and also set it to identity. It is good for debugging because the Identity object
            // is dumped by Tracy.
            $identity->__set('role', $this->permissionMap->convert($identity->getRoles()[0]));
            // With this we can access to user name and other data specified in authenticator class
            // (numbered role also!).
            $this->template->userInfo = $identity->getData();
            $this->template->userInfo['login'] = $identity->getId();
        }
        // Sanity case when user is not logged and userInfo variable is not created.
        else {
            $this->template->userInfo = ['role' => 0];
        }

        // Copy complex user information to use in presenter also.
        $this->userInfo = $this->template->userInfo;
        // Add permission map.
        $this->template->permissionMap = $this->permissionMap;
    }

    public function createComponentLogin() {
        /**Main login form. */
        $form = new Form;
        $form->addText('login')
            ->setRequired('Login is required.');
        $form->addPassword('password')
            ->setRequired('Password is required.');
        $form->addSubmit('send');
        $form->onSuccess[] = [$this, 'loginSucceeded'];

        return $form;
    }

    public function loginSucceeded(Form $form, $values) {
        /**Check data from login form. */
        try {
            $this->getUser()->login($values->login, $values->password);
            $this->redirect('this');
        }
        catch (Nette\Security\AuthenticationException $e) {
            $this->flashMessage('Bad login.');
        }
    }

    public function actionLogout() {
        /**Logout current logged user. */
        $this->getUser()->logout(true);
        $this->redirect('Homepage:', ['userid' => null]);
    }
}