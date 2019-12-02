<?php
/** Presenter for listing tasks
 * @author Michal Pospíšil
 * @email xpospi95@stud.fit.vutbr.cz
 */
namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;
use Nette\Application\UI\Form;

class UserDetailPresenter extends Model\MasterPresenter
{
    public $userDetails;

    private $userEdit;

    public function __construct(Nette\Database\Context $database, Model\User $user) {
        parent::__construct();
        $this->userDetails = new Model\UserDetail($database, $user);
        $this->userEdit = new Model\UserEdit($database, $user);
    }

    /** Renders the page on load
     * @author xpospi95
     * @param $userid: id of user to show
     */
    public function renderDefault($userid): void
    {
        if ($this->userDetails->isUser($userid) == null) {
            $this->template->invalidUser = true;
        }
        $this->template->userDetails = $this->userDetails->getUserData($userid);
        $this->template->userType = $this->userDetails->getUserType($userid);
        $this->template->supervisor = $this->userDetails->getSupervisor($userid);
    }

    public function renderEdit($userid): void
    {
        if ($this->userDetails->isUser($userid) == null) {
            $this->template->invalidUser = true;
        }
        $this->template->userDetails = $this->userDetails->getUserData($userid);
        $this->template->userType = $this->userDetails->getUserType($userid);
        $this->template->supervisor = $this->userDetails->getSupervisor($userid);
    }

    public function actionDelete($userid): void
    {
        $this->userEdit->deleteUser($userid);
        $this->redirect("Users:");
    }

    /** Creates the edit user form
     * @author xpospi95
     */
    public function createComponentEditUser() : Form {
        $form = new Form;
        $form->addText('fname')
            ->addRule(Form::MAX_LENGTH, "First name can't be longer than 32 characters.", 32)
            ->setRequired("First name is required.");
        $form->addText('sname')
            ->addRule(Form::MAX_LENGTH, "Surname can't be longer than 64 characters.", 64)
            ->setRequired("Surname is required.");
        $form->addText('mail')
            ->addRule(Form::EMAIL, "This isn't a valid e-mail.")
            ->addRule(Form::MAX_LENGTH, "Email can't be longer than 256 characters.", 256)
            ->setRequired("E-mail is required.");
        if($this->userDetails->getUserType($this->getParameter("userid")) == "customer") {
            $form->addText('company')
                ->addRule(Form::MAX_LENGTH, "Company can't be longer than 64 characters.", 64)
                ->addRule(Form::REQUIRED);
        }
        $form->addText('login')
            ->addRule(Form::MAX_LENGTH, "Login can't be longer than 32 characters.", 32)
            ->addRule(Form::REQUIRED)
            ->setDisabled(true);
        $form->addSelect('roleSelect', "Role", $this->userEdit->getRoleArr($this->getParameter("userid")));
        $form->addSelect('supSelect', "Supervisor", $this->userEdit->getSupervisorArr($this->getParameter("userid")));
        $form->addSubmit('save', 'Save');
        $form->onSuccess[] = [$this, 'performEditUser'];
        return $form;
    }

    public function performEditUser(Form $form, \stdClass $values): void {
        Debugger::barDump($values);
        $this->userEdit->editUser($this->getParameter("userid"), $values);
        if($this->userInfo['role'] < 5) {
            $this->redirect("Homepage:");
        }
        else {
            $this->redirect("Users:");
        }

    }
}