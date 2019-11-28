<?php

namespace App\Model;

use Nette;

class viewTasks extends viewBase {
    protected function selectSearched($search, Nette\Database\Table\Selection $table) {
        if ($search) {
            $search = '%' . $search . '%';
            return $table->where("name LIKE ? OR description LIKE ?", $search, $search);
        }
        return $table;
    }

    protected function openTable($orderStr) {
        return $this->database->table('task')
            ->order($orderStr)
            ->limit($this->paginator->getLength(), $this->paginator->getOffset());
    }

    protected function createOrderStr($orderBy)
    {
        switch($orderBy) {
            case "name":
            default:
                return "name";
        }
    }
}