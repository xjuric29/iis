<?php
/** Presenter for listing tasks
 * @author Michal Pospíšil
 * @email xpospi95@stud.fit.vutbr.cz
 */
namespace App\Presenters;

use App\Model\MasterPresenter;
use Nette;


abstract class ListPresenter extends MasterPresenter
{
    /** @persistent */
    public $orderBy;
    /** @persistent */
    public $orderDir;
    /** @persistent */
    public $search;
    /** @persistent */
    public $userid;

    /** Renders the page on load
     * @author xpospi95
     * @param $orderBy: ordering parameter, name and date for now, values defined in this function
     * @param $orderDir: ordering direction, desc(ending) or asc(ending)
     * @param $page: number of page to render, first is default
     * @param $search: key that is searched in name and description of ticket table
     * @param $userid: filters ticket from this user
     */
    abstract public function renderDefault($orderBy, $orderDir, $page = 1, $search = null, $userid = null): void;

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
    abstract public function performSearch(Nette\Application\UI\Form $form, \stdClass $values): void;
}