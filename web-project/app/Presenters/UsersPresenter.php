<?php
/** Presenter for listing tasks
 * @author Michal Pospíšil
 * @email xpospi95@stud.fit.vutbr.cz
 */
namespace App\Presenters;

use Nette;
use App\Model;
use Tracy\Debugger;


class UsersPresenter extends ListPresenter
{
    /** @var Model\ViewUsers @inject */
    public $sysusers;

    /** Renders the page on load
     * @author xpospi95
     * @param $orderBy: ordering parameter, name and date for now, values defined in this function
     * @param $orderDir: ordering direction, desc(ending) or asc(ending)
     * @param $page: number of page to render, first is default
     * @param $search: key that is searched in name and description of ticket table
     * @param $userid: filters ticket from this user
     */
    public function renderDefault($orderBy, $orderDir, $page = 1, $search = null, $userid = null): void
    {
        $this->template->usersList = $this->sysusers->getUsersTable($orderBy, $page, $search, $userid, $orderDir = "asc");
        $this->template->rowCount = $this->sysusers->rowCount;
        $this->template->paginator = $this->sysusers->paginator;
    }

    /** Handles search request
     * @author xpospi95
     * @param $form: Nette Form object
     * @param $values: values entered into form
     * @throws ...
     */
    public function performSearch(Nette\Application\UI\Form $form, \stdClass $values): void
    {
        $this->redirect('Users:', ['search' => $values->searchBox, 'page' => '1']);
    }
}