<?php

namespace App\Model;

use Nette;

abstract class viewBase {
    protected $database;

    public $paginator;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
        $this->paginator = new Nette\Utils\Paginator;
    }
    abstract protected function createOrderStr($orderBy);
    abstract protected function openTable($orderStr);
    abstract protected function selectSearched($search, Nette\Database\Table\Selection $table);

    public function getTicketTable($orderBy, $orderDir, $page, $search, $userid) {
        $this->paginator->setItemsPerPage(10); // počet položek na stránce
        $this->paginator->setPage($page); // číslo aktuální stránky

        $orderStr = $this->createOrderStr($orderBy);

        switch($orderDir) {
            case "asc":
                $orderStr .= " ASC";
                break;
            case "desc":
            default:
                $orderStr .= " DESC";
        }
        $retval = $this->openTable($orderStr);

        $retval = $this->selectSearched($search, $retval);


        if ($userid) {
            $retval = $retval->where("$this->urlToDbDict['userid'] LIKE ?", $userid);
        }

        $this->paginator->setItemCount($retval->count('*')); // celkový počet riadkov

        return $retval;
    }
}