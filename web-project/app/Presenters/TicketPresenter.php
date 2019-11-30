<?php
/**
 * IIS project 2019
 * Description: Presenter for displaying ticket detail and its actions.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Presenters;

use App\Model\MasterPresenter;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class TicketPresenter extends MasterPresenter {
    /** @var \App\Model\ViewTickets @inject */
    public $tickets;
    /** @var \App\Model\TicketComments @inject */
    public $comments;

    public function renderDefault($id) {
        $this->template->ticket = $this->tickets->getTicket($id);
        $this->template->comments = $this->comments->getComments($id);

        Debugger::barDump($this->template->ticket, 'Ticket');
        Debugger::barDump($this->template->comments, 'Comments array');
    }

    public function actionChangeState($id, $newState) {
        /**Method which is called by manager or higher permission from ticket detail page.
         * @param $id: Specific ticket id.
         * @param $newState: New state of specified ticket. */
        if ($this->userInfo['role'] >= $this->permissionMap->convert('manager')) {
            $this->tickets->;
        }
        else $this->flashMessage('You don\'t have enough permission to do this action');
    }

    public function createComponentComment() {
        /**Comment form for ticket. */
        $form = new Form;
        // Hidden operational inputs.
        $form->addHidden('ticketId');
        $form->addHidden('author');
        // One and only visible "input".
        $form->addTextArea('comment')
            ->setRequired('Comment cannot be blank.');
        $form->addSubmit('send');
        $form->onSuccess[] = [$this, 'commentSucceeded'];

        return $form;
    }

    public function commentSucceeded(Form $form, $values) {
        /**Process data from comment form. */
        $this->comments->addComment($values->ticketId, $values->author, $values->comment);
    }
}