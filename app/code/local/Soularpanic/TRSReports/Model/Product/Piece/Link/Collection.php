<?php
class Soularpanic_TRSReports_Model_Product_Piece_Link_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('trsreports/product_piece_link');
    }

}