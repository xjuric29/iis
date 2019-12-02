<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;

class ViewTasks extends ListModel {
    // For converting pretty and db values of ticket state.
    private $stateMap = [
        'to_do' => 'to do',
        'in_progress' => 'in progress',
        'done' => 'done'
    ];

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

    protected function filterByUser($userid, Nette\Database\Table\Selection $table) {
        return $table->where("author LIKE ?", $userid);
    }

    # xjuric29 methods
    public function getTask($id) {
        /**Return data for specific task.
         * @param $id: Specific task id. */
        $query = $this->database->table('task')->where('id = ?', $id);

        // Modify state value for displaying in sites.
        $rowValues = $query->fetch()->toArray();
        $rowValues['state'] = $this->convertDbStateToPretty($rowValues['state']);
        $rowValues['spentTime'] = $this->taskProgress->getTaskSpentTime($id);
        $rowValues['estimated_time'] = Date::convertDateIntervalToHoursMinutes($rowValues['estimated_time']);

        return new Nette\Database\Table\ActiveRow($rowValues, $query);
    }

    public function addTask($authorId, $ticketId, $assignee, $name, $description, $estimatedTime) {
        /**Create new ticket.
         * @param $authorId: String with user id.
         * @param $ticketId: Specific ticket id.
         * @param $assignee: String with user id.
         * @param $name: String with name of task.
         * @param $description: String with description of ticket.
         * @param $estimatedTime: DateInterval object.
         */
        return $this->database->table('task')->insert([
            'author' => $authorId,
            'ticket' => $ticketId,
            'worker' => $assignee,
            'name' => $name,
            'description' => $description,
            'estimated_time' => $estimatedTime
        ]);
    }

    public function updateTask($id, $authorId, $ticketId, $assignee, $name, $description, $estimatedTime) {
        /**Update task.
         * @param $id: Specific task id.
         * @param $authorId: String with user id.
         * @param $ticketId: Specific ticket id.
         * @param $assignee: String with user id.
         * @param $name: String with name of task.
         * @param $description: String with description of ticket.
         * @param $estimatedTime: DateInterval object. */
        return $this->database->table('task')->where('id = ?', $id)->update([
            'author' => $authorId,
            'ticket' => $ticketId,
            'worker' => $assignee,
            'name' => $name,
            'description' => $description,
            'estimated_time' => $estimatedTime
        ]);
    }

    public function deleteTask($id) {
        /**Delete task.
         * @param $id: Specific task id. */
        $this->database->table('task')->where('id = ?', $id)->delete();
    }

    public function updateState($id, $state) {
        /**Update state of specific task.
         * @param $id: Specific task id.
         * @param $state: Raw state string from DB. */
        $this->database->table('task')->where('id = ?', $id)
            ->update(['state' => $state]);
    }

    public function convertDbStateToPretty($dbState) {
        /**Convert state from DB enum to string which can be display for users of site.
         * @param $dbState: Raw state string from DB. */
        $state = 'to do';

        if (isset($this->stateMap[$dbState])) $state = $this->stateMap[$dbState];

        return $state;
    }

    public function convertPrettyStateToDb($prettyState) {
        /**Convert state from site to enum string which can be saved in db.
         * @param $prettyState: Pretty string from site. */
        $state = 'to_do';
        $swappedMap = array_flip($this->stateMap);

        if (isset($swappedMap[$prettyState])) $state = $swappedMap[$prettyState];

        return $state;
    }
}