<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;


final class TicketsPresenter extends Nette\Application\UI\Presenter
{
    private $database;
    public $title = "Hello";

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    public function renderDefault(): void
    {
        $this->template->ticketList = $this->database->table('ticket')
            ->order('creation_date DESC')
            ->limit(20);
    }
}