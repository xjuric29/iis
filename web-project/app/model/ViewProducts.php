<?php

namespace App\Model;

use Nette;

class ViewProducts extends ListModel
{
    protected function selectSearched($search, Nette\Database\Table\Selection $table)
    {
        if ($search) {
            $search = '%' . $search . '%';
            return $table->where("name LIKE ?", $search);
        }
        return $table;
    }

    protected function openTable($orderStr)
    {
        return $this->database->table('sub_product')
            ->order($orderStr)
            ->limit($this->paginator->getLength(), $this->paginator->getOffset());
    }

    protected function createOrderStr($orderBy)
    {
        switch ($orderBy) {
            case "lead":
                return "leader";
            case "name":
            default:
                return "name";
        }
    }
}