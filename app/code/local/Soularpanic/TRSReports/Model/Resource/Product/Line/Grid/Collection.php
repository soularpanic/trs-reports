<?php
class Soularpanic_TRSReports_Model_Resource_Product_Line_Grid_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('trsreports/product_line');
    }

    public function getTableAlias($select, $tableName) {
        $tables = $select->getPart('from');
        $map = function($arr) { return $arr['tableName']; };
        $_tables = array_map($map, $tables);
        $alias = array_search($tableName, $_tables);

        return $alias;
    }

    protected function _initSelect() {
        parent::_initSelect();
        $_select = $this->getSelect();
        $links = 'links';
        $products = 'products';
        $lines = $this->getTableAlias($_select, $this->getMainTable());

        $_select
            ->joinLeft([$links => $this->getTable('trsreports/product_line_link')],
                "{$links}.line_id = {$lines}.entity_id",
                [])
            ->joinLeft([$products => $this->getTable("catalog/product")],
                "{$products}.entity_id = {$links}.product_id",
                ["product_line_members" => "(GROUP_CONCAT(CONCAT_WS('::', {$products}.entity_id, {$products}.sku) ORDER BY {$products}.updated_at DESC))"])
            ->group("{$lines}.entity_id");

        Mage::log("manage product line sql:\n".$_select->__toString(), null, 'trs_reports.log');
    }

}