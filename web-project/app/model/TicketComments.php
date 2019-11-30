<?php
/**
 * IIS project 2019
 * Description: Class for gaining data about comments for specific ticket.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;
use Tracy\Debugger;

class TicketComments {
    private $database;
    private $user;

    public function __construct(Nette\Database\Context $database, User $user) {
        $this->database = $database;
        $this->user = $user;
    }

    public function getComments($ticketId) {
        /**Return comments for specific ticket.
           @param $ticketId: Specific ticket id. */
        $query = $this->database->table('event_ticket_comment')
            ->select('author, author.first_name, author.surname, content, id.creation_date')
            ->where('ticket = ?', $ticketId)->order('id.creation_date');

        $result = array();

        // Adding new value (userType) to activeRow object which is not possible to add by one SQL query.
        foreach ($query->fetchAll() as $row) {
            $userType = $this->user->getUserType($row->author);
            $userRole = '';

            // If user is worker, save his role.
            if ($userType == 'worker') {
                $worker = $this->user->getAdditionalUserData($row->author);
                $userRole = $this->user->convertDbRoleToPretty($worker->role);
            }

            // Converting original returned ActiveRow to array for creating new object of this class.
            $rowValues = $row->toArray();
            // Add own values.
            $rowValues['userType'] = $userType;
            $rowValues['userRole'] = $userRole;

            // Pushing new ActiveRow to result array.
            array_push($result, new Nette\Database\Table\ActiveRow($rowValues, $query));
        }

        return $result;
    }
}
