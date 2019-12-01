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

    public function getSubProduct($productId) {
        /**Return sub products for specific product.
         * @param $productId: Specific product id. */
        $query = $this->database->table('sub_product')->where('product = ?', $productId);

        return $query->fetchAll();
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
}