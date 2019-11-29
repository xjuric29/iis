<?php

namespace App\Model;

use Nette;

class viewTickets extends viewBase {
    protected function selectSearched($search, Nette\Database\Table\Selection $table) {
        if ($search) {
            $search = '%' . $search . '%';
            return $table->where("name LIKE ? OR description LIKE ?", $search, $search);
        }
        return $table;
    }

    protected function openTable($orderStr) {
        return $this->database->table('ticket')
            ->order($orderStr)
            ->limit($this->paginator->getLength(), $this->paginator->getOffset());
    }

    protected function createOrderStr($orderBy)
    {
        switch($orderBy) {
            case "name":
                return "name";
            case "date":
            default:
                return "creation_date";
        }
    }

    # xjuric29 methods
    public function getTicket($id) {
        /**Return data for specific ticket.
           @param $id: Specific ticket id. */
        $query = $this->database->table('ticket')->where('id = ?', $id);

        return $query->fetch();
    }
}
