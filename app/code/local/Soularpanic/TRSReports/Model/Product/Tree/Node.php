<?php
class Soularpanic_TRSReports_Model_Product_Tree_Node
    extends Mage_Core_Model_Abstract {

    protected $_productName;

    function __construct() {
        $this->_init('trsreports/product_tree_node');
    }

    public function getProductName() {
        if (!$this->_productName) {
            $productId = $this->getProductId();
            if ($productId) {
                $this->_productName = Mage::getModel('catalog/product')->load($productId)->getName();
            }
        }

        return $this->_productName;
    }

    public function getChildren() {
        return Mage::getModel('trsreports/product_tree_node')->getCollection()
            ->addFieldToFilter('parent_node_id', $this->getId());
    }

}