<?php
/**
 * IIS project 2019
 * Description: Class for gaining data about work logs for specific task.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;

class TaskProgress {
    private $database;
    private $user;

    public function __construct(Nette\Database\Context $database, User $user) {
        $this->database = $database;
        $this->user = $user;
    }

    public function getLoggedWork($taskId) {
        /**Return logged work for specific ticket.
         * @param $taskId: Specific task id. */
        $query = $this->database->table('event_progress_update')
            ->select('worker, worker.first_name, worker.surname, description, id.creation_date, time_from, 
            time_to')->where('task = ?', $taskId)->order('id.creation_date DESC');

        $result = array();

        // Adding new value (userRole) to activeRow object which is not possible to add by one SQL query.
        foreach ($query->fetchAll() as $row) {
            $worker = $this->user->getAdditionalUserData($row->worker);
            $userRole = $this->user->convertDbRoleToPretty($worker->role);

            // Converting original returned ActiveRow to array for creating new object of this class.
            $rowValues = $row->toArray();
            // Add own values.
            $rowValues['userRole'] = $userRole;
            $rowValues['spentTime'] = $this->getSpentTime($row->time_from, $row->time_to);

            // Pushing new ActiveRow to result array.
            array_push($result, new Nette\Database\Table\ActiveRow($rowValues, $query));
        }

        return $result;
    }

    public function addWorkLog($taskId, $worker, $startDate, $endDate, $description) {
        /**Add comment to database.
         * @param $taskId: Specific task id.
         * @param $worker: Worker id.
         * @param $startDate: Start date of work log in Nette\Utils\DateTime.
         * @param $endDate: End date of work log in Nette\Utils\DateTime.
         * @param $description: Worker comment. */
        $this->database->table('event_progress_update')->insert([
            'task' => $taskId,
            'worker' => $worker,
            'time_from' => $startDate,
            'time_to' => $endDate,
            'description' => $description
        ]);
    }

    public function getTaskSpentTime($taskId) {
        /**Get sum of spent time for specific task.
         * @param $taskId: Specific task id. */
        $sumInSeconds = 0;
        $workLogs = $this->getLoggedWork($taskId);

        foreach ($workLogs as $workLog) {
            // DateInterval inside.
            $interval = $workLog->spentTime;
            $sumInSeconds += Date::convertDateIntervalToSeconds($interval);
        }

        return Date::convertDateIntervalToHoursMinutes(Date::converSecondsToDateInterval($sumInSeconds));
    }

    public function getSpentTime($from, $to) {
        /**Return difference between this two times.
         * @param $from: Specific ticket id.
         * @param $to: Author id. */
        // Timestamps.
        $bigger = $from->getTimestamp();
        $smaller = $to->getTimestamp();

        if ($smaller > $bigger) {
            $temp = $smaller;
            $smaller = $bigger;
            $bigger = $temp;
        }

        $dateTimeBigger = new \DateTime();
        $dateTimeBigger->setTimestamp($bigger);
        $dateTimeSmaller = new \DateTime();
        $dateTimeSmaller->setTimestamp($smaller);

        return $dateTimeBigger->diff($dateTimeSmaller);
    }
}
