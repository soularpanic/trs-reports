<?php
class Soularpanic_TRSReports_Model_Resource_Product_Tree_Node
    extends Mage_Core_Model_Resource_Db_Abstract {

    protected function _construct() {
        $this->_init('trsreports/product_tree_node', 'entity_id');
    }
}