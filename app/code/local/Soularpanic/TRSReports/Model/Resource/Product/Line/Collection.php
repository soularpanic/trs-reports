<?php
class Soularpanic_TRSReports_Model_Resource_Product_Line_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('trsreports/product_line');
    }

}