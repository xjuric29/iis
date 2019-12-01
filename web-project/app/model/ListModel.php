<?php

namespace App\Model;

use Nette;

abstract class ListModel {
    protected $database;

    public $paginator;
    public $rowCount;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
        $this->paginator = new Nette\Utils\Paginator;
    }
    abstract protected function createOrderStr($orderBy);
    abstract protected function openTable($orderStr);
    abstract protected function selectSearched($search, Nette\Database\Table\Selection $table);
    abstract protected function filterByUser($userid, Nette\Database\Table\Selection $table);

    public function getTable($orderBy, $orderDir, $page, $search, $userid) {
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

        if ($search) {
            $retval = $this->selectSearched($search, $retval);
        }

        if ($userid) {
            $retval = $this->filterByUser($userid, $retval);
        }

        $this->rowCount = $retval->count('*');
        $this->paginator->setItemCount($this->rowCount); // celkový počet riadkov

        return $retval;
    }
}