<?php
/**
 * IIS project 2019
 * Description: Presenter for displaying ticket detail and its actions.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Presenters;

use App\Model\Date;
use App\Model\MasterPresenter;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class TaskPresenter extends MasterPresenter {
    /** @var \App\Model\ViewTasks @inject */
    public $tasks;
    /** @var \App\Model\TaskProgress @inject */
    public $workLogs;
    /** @var \App\Model\ViewTickets @inject */
    public $ticket;
    /** @var \App\Model\User @inject */
    public $user;

    // Task detail.
    public function actionDefault($id) {
        /**Check permission before access to this page. */
        if ($this->userInfo['role'] <= $this->permissionMap->convert('customer')) {
            $this->flashMessage('You are not permitted to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function renderDefault($id) {
        $this->template->task = $this->tasks->getTask($id);
        Debugger::barDump($this->template->task, 'task');
        $this->template->workLogs = $this->workLogs->getLoggedWork($id);
        Debugger::barDump($this->template->workLogs, 'workLogs');
    }

    public function actionChangeState($id, $newState) {
        /**Change state of task.
         * @param $id: Specific task id.
         * @param $newState: New state of specified task. */
        if ($this->userInfo['role'] >= $this->permissionMap->convert('common_worker')) {
            $this->tasks->updateState($id, $newState);
            $this->redirect(':', $id);
        }
        else {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function actionDelete($id) {
        /**Delete specific task.
         * @param $id: Specific task id. */
        $task = $this->tasks->getTask($id);

        if ($this->userInfo['role'] >= $this->permissionMap->convert('manager') &&
            $task->author == $this->userInfo['login']) {
            $this->tasks->deleteTask($id);
            $this->redirect('Tasks:', ['creator' => $this->userInfo['login']]);
        }
        else {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function createComponentWorkLog() {
        /**Comment form for ticket. */
        $form = new Form;
        // Hidden operational inputs.
        $form->addHidden('taskId');
        $form->addHidden('worker');
        // One and only visible "input".
        $form->addText('date')
            #->addRule(Form::PATTERN,'Date is in bad format.', '([0-9]{2}\.){2}[0-9]{4}')
            ->setRequired('Date cannot be blank.');
        $form->addText('fromTime')
            ->addRule(Form::PATTERN,'Start time is in in bad format.', '[0-9]{2}:[0-9]{2}')
            ->setRequired('Start time cannot be blank.');
        $form->addText('toTime')
            ->addRule(Form::PATTERN,'End time is in in bad format.', '[0-9]{2}:[0-9]{2}')
            ->setRequired('End time cannot be blank.');
        $form->addTextArea('description')
            ->setRequired('Description cannot be blank.');
        $form->addSubmit('send');
        $form->onSuccess[] = [$this, 'workLogSucceeded'];

        return $form;
    }

    public function workLogSucceeded(Form $form, $values) {
        /**Process data from work log form. */
        // Sanity state when start time is greater than end time. It is validated here because nette form does not have
        // this type of check in basic rules.
        if ($values->fromTime > $values->toTime) $this->redirect(':', $values->taskId);

        $this->workLogs->addWorkLog($values->taskId, $values->worker,
            new DateTime($values->date . ' ' . $values->fromTime),
            new DateTime($values->date . ' ' . $values->toTime), $values->description);

        // Switch state to in progress if is in to do.
        if ($this->tasks->getTask($values->taskId)->state != 'in progress') $this->tasks->updateState($values->taskId, 'in_progress');
    }

    // Add/edit task.
    public function actionAdd() {
        /**Check permission before access to this page. */
        if ($this->userInfo['role'] < $this->permissionMap->convert('manager')) {
            $this->flashMessage('You are not permitted to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function actionEdit($id) {
        /**Check permission before access to this page. */
        $task = $this->tasks->getTask($id);

        if ($this->userInfo['role'] < $this->permissionMap->convert('manager') ||
            $task->author != $this->userInfo['login']) {
            $this->flashMessage('You are not permitted to do this action.');
            $this->redirect('Homepage:');
        }
        // Fill form.
        else {
            $this['task']->setDefaults([
                'name' => $task->name,
                'assignee' => $task->worker,
                'ticket' => $task->ticket,
                'estimatedTime' => $task->estimated_time,
                'description' => $task->description
            ]);
        }
    }

    public function renderEdit($id) {
        // Task only for id for delete param.
        $this->template->task = $this->tasks->getTask($id);
    }

    public function createComponentTask() {
        /**Task form. */
        $form = new Form;
        $form->addText('name')
            ->setRequired('Name of task cannot be blank.');
        $form->addSelect('assignee', null, $this->user->getArrayForAssigneeSelect());
        $form->addSelect('ticket', null, $this->ticket->getArrayForTicketSelect($this->userInfo['login']));
        $form->addText('estimatedTime')
            ->addRule(Form::PATTERN,'Estimated time is in in bad format.', '[0-9]{2}:[0-9]{2}')
            ->setRequired('Estimated time cannot be blank.');
        $form->addTextArea('description')
            ->setRequired('Description cannot be blank.');
        $form->addSubmit('send');
        $form->onSuccess[] = [$this, 'taskCreateUpdateSucceeded'];

        return $form;
    }

    public function taskCreateUpdateSucceeded(Form $form, $values) {
        /**Process data from task form. */
        // Important part. If 'id' is not set the form is used for creating ticket else for editing.
        $id = $this->getParameter('id');

        // Task is created.
        if (!$id) {
            // Create ticket.
            $task = $this->tasks->addTask($this->userInfo['login'], $values->ticket, $values->assignee, $values->name,
                $values->description, Date::convertHoursMinutesToDateInterval($values->estimatedTime));
            // Set for image save.
            $id = $task->id;
        }
        // Task is edited.
        else {
            $this->tasks->updateTask($id, $this->userInfo['login'], $values->ticket, $values->assignee, $values->name,
                $values->description, Date::convertHoursMinutesToDateInterval($values->estimatedTime));
        }

        // Redirect to the ticket.
        $this->redirect("Task:", $id);
    }
}