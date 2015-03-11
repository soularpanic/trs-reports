<?php
class Soularpanic_TRSReports_Model_Resource_Product_Line
    extends Mage_Core_Model_Resource_Db_Abstract {

    /**
     * Resource initialization
     */
    protected function _construct() {
        $this->_init('trsreports/product_line', 'entity_id');
    }

}