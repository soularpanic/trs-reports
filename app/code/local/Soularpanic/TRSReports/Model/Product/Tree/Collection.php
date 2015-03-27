<?php
class Soularpanic_TRSReports_Model_Product_Tree_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function __construct() {
        $this->_init('trsreports/product_tree');
    }

}