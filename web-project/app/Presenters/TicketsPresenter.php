<?php

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

    public function renderDefault($orderBy, $orderDir, $page = 1): void
    {
        $this->template->ticketList = $this->tickets->getTicketTable($orderBy, $orderDir, $page);
        $this->template->paginator = $this->tickets->paginator;
    }
}