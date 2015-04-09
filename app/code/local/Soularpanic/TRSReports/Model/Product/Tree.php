<?php
class Soularpanic_TRSReports_Model_Product_Tree
    extends Mage_Core_Model_Abstract {

    function __construct() {
        $this->_init('trsreports/product_tree');
    }


    public function getRootNodes() {
        return $this->getResource()->getRootNodes($this->getId());
    }
}