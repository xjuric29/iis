<?php
/**
 * IIS project 2019
 * Description: Presenter for product and sub product modify.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Presenters;

use App\Model\Date;
use App\Model\MasterPresenter;
use Nette\Application\UI\Form;
use Tracy\Debugger;

class ProductPresenter extends MasterPresenter {
    /** @var \App\Model\Product @inject */
    public $products;
    /** @var \App\Model\User @inject */
    public $users;

    // Product.
    public function actionAddProduct() {
        /**Add product. */
        if ($this->userInfo['role'] < $this->permissionMap->convert('superior')) {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function actionEditProduct($id) {
        /**Edit product.
         * @param $id: Specific product id. */
        $product = $this->products->getProduct($id);

        if ($this->userInfo['role'] < $this->permissionMap->convert('superior')) {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }

        $this['product']->setDefaults([
            'name' => $product->name
        ]);
    }

    public function renderEditProduct($id) {
        $this->template->product = $this->products->getProduct($id);
    }

    public function actionDeleteProduct($id) {
        /**Delete specific product.
         * @param $id: Specific product id. */

        if ($this->userInfo['role'] >= $this->permissionMap->convert('superior')) {
            $this->products->deleteProduct($id);
            $this->redirect('Products:');
        }
        else {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function createComponentProduct() {
        /**Comment form for ticket. */
        $form = new Form;
        $form->addText('name')
            ->setRequired('Name cannot be blank.');
        $form->onSuccess[] = [$this, 'productSucceeded'];

        return $form;
    }

    public function productSucceeded(Form $form, $values) {
        /**Process data from task form. */
        // Important part. If 'id' is not set the form is used for creating ticket else for editing.
        $id = $this->getParameter('id');

        // Product is created.
        if (!$id) {
            // Create product.
            $product = $this->products->addProduct($values->name);
            // Set for image save.
            $id = $product->id;
        }
        // Task is edited.
        else {
            $this->products->updateProduct($id, $values->name);
        }

        // Redirect to product list.
        $this->redirect("Products:");
    }

    // Sub product.
    public function actionAddSubProduct() {
        /**Add sub product. */
        if ($this->userInfo['role'] < $this->permissionMap->convert('superior')) {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function actionEditSubProduct($id) {
        /**Edit sub product.
         * @param $id: Specific subproduct id. */
        $subProduct = $this->products->getSubProduct(null, null, $id);

        if ($this->userInfo['role'] < $this->permissionMap->convert('superior')) {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }

        $this['subProduct']->setDefaults([
            'product' => $subProduct->product,
            'name' => $subProduct->name,
            'leader' => $subProduct->leader
        ]);
    }

    public function renderEditSubProduct($id) {
        $this->template->subProduct = $this->products->getSubProduct(null, null, $id);
    }

    public function actionDeleteSubProduct($id) {
        /**Delete specific product.
         * @param $id: Specific product id. */

        if ($this->userInfo['role'] >= $this->permissionMap->convert('superior')) {
            $this->products->deleteSubProduct($id);
            $this->redirect('Products:');
        }
        else {
            $this->flashMessage('You don\'t have enough permission to do this action.');
            $this->redirect('Homepage:');
        }
    }

    public function createComponentSubProduct() {
        /**Comment form for ticket. */
        $form = new Form;
        $form->addSelect('product', null, $this->products->getArrayForProductSelect());
        $form->addText('name')
            ->setRequired('Name cannot be blank.');
        $form->addSelect('leader', null, $this->users->getArrayForLeaderSelect());
        $form->onSuccess[] = [$this, 'subProductSucceeded'];

        return $form;
    }

    public function subProductSucceeded(Form $form, $values) {
        /**Process data from task form. */
        // Important part. If 'id' is not set the form is used for creating ticket else for editing.
        $id = $this->getParameter('id');

        // Sub product is created.
        if (!$id) {
            // Create sub product.
            $subProduct = $this->products->addSubProduct($values->product, $values->leader, $values->name);
            $id = $subProduct->id;
        }
        // Task is edited.
        else {
            $this->products->updateSubProduct($id, $values->product, $values->leader, $values->name);
        }

        // Redirect to product list.
        $this->redirect("Products:");
    }
}