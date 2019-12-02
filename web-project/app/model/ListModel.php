<?php

namespace App\Model;

use Nette;

abstract class ListModel {
    protected $database;
    protected $taskProgress;
    protected $product;

    public $paginator;
    public $rowCount;

    public function __construct(Nette\Database\Context $database, TaskProgress $taskProgress, Product $product) {
        $this->database = $database;
        $this->taskProgress = $taskProgress;
        $this->product = $product;
        $this->paginator = new Nette\Utils\Paginator;
    }
    abstract protected function createOrderStr($orderBy);
    abstract protected function openTable($orderStr);
    abstract protected function selectSearched($search, Nette\Database\Table\Selection $table);

    protected function filterByUser($userid, Nette\Database\Table\Selection $table) {
        return $table->where("author LIKE ?", $userid);
    }

    protected function filterByAssignee($assid, Nette\Database\Table\Selection $table) {
        return $table;
    }

    public function getTable($orderBy, $orderDir, $page, $search, $userid, $assid) {
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

        if ($assid) {
            $retval = $this->filterByAssignee($assid, $retval);
        }

        $this->rowCount = $retval->count('*');
        $this->paginator->setItemCount($this->rowCount);

        return $retval;
    }
}