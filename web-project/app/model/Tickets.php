<?php

namespace App\Model;

use Nette;

class Tickets {
    private $database;

    public $paginator;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    private function getTicketCount() {
        return $this->database->fetchField('SELECT COUNT(*) FROM ticket');
    }

    public function getTicketTable($orderBy, $orderDir, $page = 1) {
        $articlesCount = $this->getTicketCount();
        $this->paginator = new Nette\Utils\Paginator;
        $this->paginator->setItemCount($articlesCount); // celkový počet článků
        $this->paginator->setItemsPerPage(10); // počet položek na stránce
        $this->paginator->setPage($page); // číslo aktuální stránky

        switch($orderBy) {
            case "name":
                $orderStr = "name";
                break;
            case "date":
            default:
                $orderStr = "creation_date";
        }
        switch($orderDir) {
            case "asc":
                $orderStr .= " ASC";
                break;
            case "desc":
            default:
                $orderStr .= " DESC";
        }

        return $this->database->table('ticket')
            ->order($orderStr)
            ->limit($this->paginator->getLength(), $this->paginator->getOffset());
    }

}