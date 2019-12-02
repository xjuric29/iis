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

    # xjuric29 methods
    public function getTicket($id) {
        /**Return data for specific ticket.
         * @param $id: Specific ticket id. */
        $query = $this->database->table('ticket')->where('id = ?', $id);

        // Modify state value for displaying in sites.
        $rowValues = $query->fetch()->toArray();
        $rowValues['state'] = $this->convertDbStateToPretty($rowValues['state']);

        return new Nette\Database\Table\ActiveRow($rowValues, $query);
    }

    public function addTicket($authorId, $subProductId, $name, $description) {
        /**Create new ticket.
         * @param $authorId: String with user id.
         * @param $subProductId: Specific sub product id.
         * @param $name: String with name of ticket.
         * @param $description: String with description of ticket. */
        return $this->database->table('ticket')->insert([
            'author' => $authorId,
            'sub_product' => $subProductId,
            'name' => $name,
            'description' => $description
        ]);
    }

    public function updateTicket($id, $subProductId, $name, $description) {
        /**Update ticket.
         * @param $id: Specific ticket id.
         * @param $subProductId: Specific sub product id.
         * @param $name: String with name of ticket.
         * @param $description: String with description of ticket. */
        return $this->database->table('ticket')->where('id = ?', $id)->update([
            'sub_product' => $subProductId,
            'name' => $name,
            'description' => $description
        ]);
    }

    public function updateState($id, $state) {
        /**Update state of specific ticket.
         * @param $id: Specific ticket id.
         * @param $state: Raw state string from DB. */
        $this->database->table('ticket')->where('id = ?', $id)
            ->update(['state' => $state]);
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

        if (isset($swappedMap[$prettyState])) $state = $swappedMap[$prettyState];

        return $state;
    }
}
