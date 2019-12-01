<?php
/** Presenter for listing tasks
 * @author Michal Pospíšil
 * @email xpospi95@stud.fit.vutbr.cz
 */
namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;

class UserDetailPresenter extends Model\MasterPresenter
{
    public $userDetails;

    private $sysOptions;

    public function __construct(Nette\Database\Context $database, Model\User $user) {
        parent::__construct();
        $this->userDetails = new Model\UserDetail($database, $user);
        $this->sysOptions = new Model\UserEdit($database, $user);
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

    /** Creates the edit user form
     * @author xpospi95
     */
    public function createComponentEditUser() : Nette\Application\UI\Form {
        $roleArr = array("Administrator", "Manager", "Supervisor", "Employee", "Customer");

        $form = new Nette\Application\UI\Form;
        $form->addText('fname')
            ->addRule(Nette\Application\UI\Form::MAX_LENGTH, "First name can't be longer than 32 characters.", 32);
        $form->addText('sname')
            ->addRule(Nette\Application\UI\Form::MAX_LENGTH, "Surname can't be longer than 64 characters.", 32);
        $form->addText('mail')
            ->addRule(Nette\Application\UI\Form::EMAIL, "This isn't a valid e-mail.")
            ->addRule(Nette\Application\UI\Form::MAX_LENGTH, "Email can't be longer than 256 characters.", 256);
        $form->addText('login')
            ->addRule(Nette\Application\UI\Form::MAX_LENGTH, "Login can't be longer than 32 characters.", 64);
        $form->addSelect('roleSelect', "Role", $roleArr);
        $form->addSelect('supSelect', "Supervisor", $this->sysOptions->getSupervisors());
        $form->addSubmit('save', 'Save');
        $form->addSubmit('cancel', 'Cancel');
        $form->addSubmit('delete', 'Delete');
        $form->onSuccess[] = [$this, 'performSearch'];
        return $form;
    }
}