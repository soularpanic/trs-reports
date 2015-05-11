<?php
class Soularpanic_TRSReports_Model_Resource_Product_Tree
    extends Mage_Core_Model_Resource_Db_Abstract {

    protected function _construct() {
        $this->_init('trsreports/product_tree', 'entity_id');
    }


    public function getRootNodes($treeId) {
        return $this->getNodes($treeId, null);
    }


    public function getNodes($treeId, $parentNodeId = null) {
        $parentCond = $parentNodeId ? ['eq' => $parentNodeId] : ['null' => true];
        $rootNodes = Mage::getModel('trsreports/product_tree_node')
            ->getCollection()
            ->addFieldToFilter('parent_node_id', $parentCond)
            ->addFieldToFilter('tree_id', ['eq' => $treeId]);
        return $rootNodes;
    }
}