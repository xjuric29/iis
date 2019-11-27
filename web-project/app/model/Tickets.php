<?php

namespace App\Model;

use Nette;

class Tickets {
    private $database;

    public $paginator;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function getTicketTable($orderBy, $orderDir, $page = 1, $search = null) {
        $this->paginator = new Nette\Utils\Paginator;
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

        $retval =  $this->database->table('ticket')
            ->order($orderStr)
            ->limit($this->paginator->getLength(), $this->paginator->getOffset());

        if ($search) {
            $retval =  $retval->where("name LIKE ?", '%' . $search . '%');
        }

        $this->paginator->setItemCount($retval->count('*')); // celkový počet riadkov

        return $retval;
    }
}