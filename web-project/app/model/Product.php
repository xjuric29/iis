<?php
/**
 * IIS project 2019
 * Description: Class for gaining access to user data.
 * Author: Jiri Jurica (xjuric29)
 */

namespace App\Model;

use Nette;

class Product {
    private $database;

    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    public function getProduct($id) {
        /**Get product.
         * @param $id: Specific product id. */
        return $this->database->table('product')->where('id = ?', $id)->fetch();
    }

    public function addProduct($name) {
        /**Add product to database.
         * @param $name: Name of product. */
        return $this->database->table('product')->insert([
            'name' => $name
        ]);
    }

    public function updateProduct($id, $name) {
        /**Edit product.
         * @param $id: Specific product id.
         * @param $name: Name of product. */
        $this->database->table('product')->where('id = ?', $id)->update([
            'name' => $name
        ]);
    }

    public function deleteProduct($id) {
        /**Delete product.
         * @param $id: Specific product id. */
        $this->database->table('product')->where('id = ?', $id)->delete();
    }

    public function getSubProduct($productId=null, $login=null, $id=null) {
        /**Return sub products for specific product or for specific manager.
         * @param $productId: Specific product id.
         * @param $login: Specific user id.
         * @param $id: Specific sub product id. */
        $query = $this->database->table('sub_product');

        if($productId) $query->where('product = ?', $productId);
        elseif($login) $query->where('leader = ?', $login);
        elseif($id) {
            $query->where('id = ?', $id);
            return $query->fetch();
        }

        return $query->fetchAll();
    }

    public function addSubProduct($productId, $leader, $name) {
        /**Add sub product to database.
         * @param $productId: Specific product id.
         * @param $leader: String with user id.
         * @param $name: Name of sub product. */
        return $this->database->table('sub_product')->insert([
            'product' => $productId,
            'leader' => $leader,
            'name' => $name
        ]);
    }

    public function updateSubProduct($id, $productId, $leader, $name) {
        /**Add sub product to database.
         * @param $id: Specific sub product id.
         * @param $productId: Specific product id.
         * @param $leader: String with user id.
         * @param $name: Name of sub product. */
        $this->database->table('sub_product')->where('id = ?', $id)->update([
            'product' => $productId,
            'leader' => $leader,
            'name' => $name
        ]);
    }

    public function deleteSubProduct($id) {
        /**Add sub product to database.
         * @param $id: Specific sub product id.  */
        $this->database->table('sub_product')->where('id = ?', $id)->delete();
    }

    public function getArrayForSubProductSelect() {
        /**Prepare 2D array with name of all Products on first level and 'id' => 'name of sub product' on second.
         * @return: Array of arrays with sub products as values. */
        $query = $this->database->table('product');
        // Result array. Example:
        // [
        //      'product1' => [
        //          1 => 'subProduct1',
        //          ...
        //          ],
        //      'product2' => [
        //          ...
        //          ],
        //       ...
        // ]
        $selectArray = array();

        // Iterate over all product.
        foreach ($query->fetchAll() as $product) {
            // Array of all sub product for the product.
            $productArray = array();

            // Iterate over all sub product in product.
            foreach ($this->getSubProduct($product->id) as $subProduct) {
                $productArray[$subProduct->id] = $subProduct->name;
            }

            // If product has at least one sub product, push to result array.
            if (count($productArray)) $selectArray[$product->name] = $productArray;
        }

        return $selectArray;
    }

    public function getArrayForProductSelect() {
        /**Prepare array with products.
         * @return: Array of arrays with products as values. */
        $query = $this->database->table('product');
        $selectArray = array();

        foreach ($query->fetchAll() as $product) {
            $selectArray[$product->id] = $product->name;
        }

        return $selectArray;
    }
}