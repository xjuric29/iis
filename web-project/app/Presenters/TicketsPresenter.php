<?php
/** Presenter for listing tickets
 * @author Michal Pospíšil
 * @email xpospi95@stud.fit.vutbr.cz
 */
namespace App\Presenters;

use Nette;


class TicketsPresenter extends Nette\Application\UI\Presenter
{
    /** @var \App\Model\Tickets @inject */
    public $tickets;

    /** @persistent */
    public $orderBy;

    /** @persistent */
    public $orderDir;

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
        $this->template->ticketList = $this->tickets->getTicketTable($orderBy, $orderDir, $page, $search, $userid);
        $this->template->paginator = $this->tickets->paginator;
    }

    /** Creates a search bar
     * @author xpospi95
     */
    public function createComponentSearch() : Nette\Application\UI\Form {
        $form = new Nette\Application\UI\Form;
        $form->addText('searchBox')
            ->addRule(Nette\Application\UI\Form::MIN_LENGTH, 'Enter at least %d characters to search.', 3);
        $form->addSubmit('searchButton', 'Search');
        $form->onSuccess[] = [$this, 'performSearch'];
        return $form;
    }

    /** Handles search request
     * @author xpospi95
     * @param $form: Nette Form object
     * @param $values: values entered into form
     * @throws ...
     */
    public function performSearch(Nette\Application\UI\Form $form, \stdClass $values): void
    {
        $this->redirect('Tickets:', ['search' => $values->searchBox, 'page' => '1']);
    }
}