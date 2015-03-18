<?php
class Soularpanic_TRSReports_Model_Product_Line_Link_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('trsreports/product_line_link');
    }

}