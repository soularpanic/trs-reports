<?php
class Soularpanic_TRSReports_Model_Resource_Report_OutOfStock_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    const TOP_LEVEL_TABLE_ALIAS = 'outOfStock';

    protected $_aggregationTable = 'cataloginventory/stock_item';

    protected $_selectedColumns = [ ];

    protected function _initSelect() {
        $_helper = Mage::helper('trsreports/collection');

        $_productLinesSelect = $_helper->getProductLinesSelect();
        $_inventory = $_helper->getProductInventory($this->_from, $this->_to);

        $_grpByProductSelect = $_helper->_getNewSelect();
        $_productLinesAlias = "productLines";
        $_inventoryAlias = "inventory";
        $_grpByProductSelect->from([ $_productLinesAlias => $_productLinesSelect ],
            [ 'product_id' => 'product_id',
                'derived_name' => "line_name",
                'derived_sku' => 'line_sku',
                'derived_id' => "(if($_productLinesAlias.tree_name is not null, concat('T-', $_productLinesAlias.tree_id), if($_productLinesAlias.piece_name is not null, concat('L-', $_productLinesAlias.piece_id), concat('P-', $_productLinesAlias.product_id))))",
            ])
            ->joinLeft([ $_inventoryAlias => $_inventory ],
                "$_inventoryAlias.product_id = $_productLinesAlias.product_id",
                [ 'qty',
                    'suppliers',
                    'purchase_orders' ]);
        $this->log("\n\n2:\n".$_grpByProductSelect->__toString());

        $_outOfStockRawSelect = $_helper->_getNewSelect();
        $_outOfStockRaw = "outOfStockRaw";
        $_outOfStockRawSelect
            ->from([ $_outOfStockRaw => $_grpByProductSelect ],
                [ 'derived_id',
                    'entity_id' => 'product_id',
                    'derived_name',
                    'derived_sku',
                    'total_qty_stock' => 'sum(ifnull(qty, 0))',
                    'suppliers',
                    'purchase_orders' => 'group_concat(purchase_orders)'
                ])
            ->group('derived_id');
        $this->log("\n\n3:\n" . $_outOfStockRawSelect->__toString());

        $outOfStockCalculated = self::TOP_LEVEL_TABLE_ALIAS;
        $_select = $this->getSelect();
        $_select
            ->from([ $outOfStockCalculated => $_outOfStockRawSelect ],
                [ 'derived_id',
                    'entity_id',
                    'derived_name',
                    'derived_sku',
                    'total_qty_stock',
                    'purchase_orders',
                    'suppliers',
                ])
            ->joinLeft([ 'catalog' => $this->getTable('catalog/product') ],
                "catalog.entity_id = $outOfStockCalculated.entity_id",
                [])
            ->joinLeft([ 'attrset' => $this->getTable("eav/attribute_set") ],
                "catalog.attribute_set_id = attrset.attribute_set_id",
                "attribute_set_name")
            ->where("total_qty_stock <= 0")
            ->where("catalog.type_id = 'simple'")
            ->where('attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")');

        $this->log("Out of Stock SQL:\n".$this->getSelect()->__toString());
    }

    protected function _applyCustomFilter() {
        $customFilterData = $this->getCustomFilterData();
        $customFilterData->setProductTable(self::TOP_LEVEL_TABLE_ALIAS);

        return parent::_applyCustomFilter();
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }
}