<?php
/**
 * IIS project 2019
 * Description: Presenter for displaying ticket detail and its actions.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Presenters;

use App\Model\MasterPresenter;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class TicketPresenter extends MasterPresenter {
    /** @var \App\Model\ViewTickets @inject */
    public $tickets;
    /** @var \App\Model\TicketComments @inject */
    public $comments;
    /** @var \App\Model\Product @inject */
    public $products;
    /** @var \App\Model\Image @inject */
    public $images;

    // Ticket detail.
    public function renderDefault($id) {
        $this->template->ticket = $this->tickets->getTicket($id);
        $this->template->comments = $this->comments->getComments($id);
        $this->template->images = $this->images->getArrayOfRealImages($id);
    }

    public function actionChangeState($id, $newState) {
        /**Method which is called by manager or higher permission from ticket detail page.
         * @param $id: Specific ticket id.
         * @param $newState: New state of specified ticket. */
        if ($this->userInfo['role'] >= $this->permissionMap->convert('manager')) {
            $this->tickets->updateState($id, $newState);
            $this->redirect(':', $id);
        }
        else {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function createComponentComment() {
        /**Comment form for ticket. */
        $form = new Form;
        // Hidden operational inputs.
        $form->addHidden('ticketId');
        $form->addHidden('author');
        // One and only visible "input".
        $form->addTextArea('comment')
            ->setRequired('Comment cannot be blank.');
        $form->addSubmit('send');
        $form->onSuccess[] = [$this, 'commentSucceeded'];

        return $form;
    }

    public function commentSucceeded(Form $form, $values) {
        /**Process data from comment form. */
        $this->comments->addComment($values->ticketId, $values->author, $values->comment);
    }

    // Add/edit ticket.
    public function actionAdd() {
        /**Check permission before access to this page. */
        if ($this->userInfo['role'] < $this->permissionMap->convert('customer')) {
            $this->flashMessage('You are not permitted to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function actionEdit($id) {
        /**Check permission before access to this page. */
        $ticket = $this->tickets->getTicket($id);

        if ($this->userInfo['role'] < $this->permissionMap->convert('customer') ||
            $ticket->author != $this->userInfo['login']) {
            $this->flashMessage('You are not permitted to do this action.');
            $this->redirect('Homepage:');
        }
        // Fill form.
        else {
            $this['ticket']->setDefaults([
                'name' => $ticket->name,
                'product' => $ticket->sub_product,
                'description' => $ticket->description
            ]);
        }
    }

    public function renderEdit($id) {
        $this->template->ticket = $this->tickets->getTicket($id);
        $this->template->images = $this->images->getArrayOfRealImages($id);
    }

    public function actionDeleteImage($image, $id) {
        /**Action for delete specific image.
         * @param $image: Relative path to image from site root.*/
        $this->images->deleteImage($image);
        $this->redirect(':edit', $id);
    }

    public function createComponentTicket() {
        /**Ticket form. */
        // Important part. If 'id' is not set the form is used for creating ticket else for editing.
        $id = $this->getParameter('id');

        $form = new Form;
        $form->addText('name')
            ->setRequired('Name of ticket cannot be blank.');
        $form->addSelect('product', null, $this->products->getArrayForSubProductSelect());
        $form->addTextArea('description')
            ->setRequired('Description cannot be blank.');

        // Ticket is created.
        if (!$id) {
            $form->addMultiUpload('files')
                ->addRule(Form::PATTERN, 'The images have to be in JPG format.',
                    '.*\.(jpg|JPG|jpeg|JPEG)')
                ->addRule(Form::LENGTH,
                    'Cannot upload more than ' . $this->images->getAllowedImageCount() . ' images.',
                    [1, $this->images->getAllowedImageCount()]);
        }
        // Ticket is edited.
        else {
            // If ticket has maximum images, disable upload button.
            if ($this->images->getFreePathCount($id) == 0) $form->addMultiUpload('files')->setDisabled(true);
            // Else allow only rest count of images.
            else {
                $form->addMultiUpload('files')
                    ->addRule(Form::PATTERN, 'The images have to be in JPG format.',
                        '.*\.(jpg|JPG|jpeg|JPEG)')
                    ->addRule(Form::LENGTH,
                        'Cannot upload more than ' . $this->images->getFreePathCount($id) . ' images.',
                        [1, $this->images->getFreePathCount($id)]);
            }
        }
        $form->addSubmit('send');
        $form->onSuccess[] = [$this, 'ticketCreateUpdateSucceeded'];

        return $form;
    }

    public function ticketCreateUpdateSucceeded(Form $form, $values) {
        /**Process data from ticket form. */
        // Important part. If 'id' is not set the form is used for creating ticket else for editing.
        $id = $this->getParameter('id');

        // Ticket is created.
        if (!$id) {
            // Create ticket.
            $ticket = $this->tickets->addTicket($this->userInfo['login'], $values->product, $values->name,
                $values->description);
            // Set for image save.
            $id = $ticket->id;
            Debugger::barDump($values->files, 'files');
        }
        // Ticket is edited.
        else {
            $this->tickets->updateTicket($id, $values->product, $values->name, $values->description);
        }

        // Save images.
        $this->images->saveImages($values->files, $id);

        // Redirect to the ticket.
        $this->redirect("Ticket:", $id);
    }
}