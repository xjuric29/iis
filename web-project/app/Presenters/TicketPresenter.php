<?php
/**
 * IIS project 2019
 * Description: Presenter for displaying ticket detail and its actions.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Presenters;

use Nette;
use Tracy\Debugger;

class TicketPresenter extends Nette\Application\UI\Presenter {
    /** @var \App\Model\Tickets @inject */
    public $tickets;
    public $comments;

    public function renderDefault($id) {
        /**
         *
         */
        $this->template->ticket = $this->tickets->getTicket($id);
    }
}