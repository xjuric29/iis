<?php
/**
 * IIS project 2019
 * Description: Presenter for displaying ticket detail and its actions.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Presenters;

use App\Model\MasterPresenter;

class TicketPresenter extends MasterPresenter {
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