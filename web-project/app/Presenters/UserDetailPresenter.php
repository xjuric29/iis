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

    public function __construct(Nette\Database\Context $database, Model\User $user) {
        parent::__construct();
        $this->userDetails = new Model\UserDetail($database, $user);
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
}