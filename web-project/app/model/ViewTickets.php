<?php

namespace App\Model;

use Nette;

class ViewTickets extends ListModel {
    // For converting pretty and db values of ticket state.
    private $stateMap = [
        'new' => 'new',
        'in_progress' => 'in progress',
        'closed' => 'closed'
    ];

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

    protected function filterByUser($userid, Nette\Database\Table\Selection $table) {
        return $table->where("author LIKE ?", $userid);
    }

    # xjuric29 methods
    public function getTicket($id) {
        /**Return data for specific ticket.
           @param $id: Specific ticket id. */
        $query = $this->database->table('ticket')->where('id = ?', $id);

        return $query->fetch();
    }

    public function updateState($id, $state) {

    }

    public function convertDbStateToPretty($dbState) {
        /**Convert state from DB enum to string which can be display for users of site.
         * @param $dbState: Raw state string from DB. */
        $state = 'new';

        if (isset($this->stateMap[$dbState])) $state = $this->stateMap[$dbState];

        return $state;
    }

    public function convertPrettyStateToDb($prettyState) {
        /**Convert state from site to enum string which can be saved in db.
         * @param $prettyState: Pretty string from site. */
        $state = 'new';
        $swappedMap = array_flip($this->stateMap);

        if (isset($swappedMap[$prettyState])) $state = $this->stateMap[$dbState];

        return $state;
    }
}
