<?php
class Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailabilityPlusTransit_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailability_Collection {

    protected function _initSelect() {

        $_select = $this->getSelect();
        $_helper = Mage::helper('trsreports/collection');

        $_productLinesSelect = $_helper->getProductLinesSelect();
        $_customerOrders = $_helper->getProductOrders($this->_from, $this->_to, $_productLinesSelect);
        $_inventory = $_helper->getProductInventory($this->_from, $this->_to);

        $_grpByProductSelect = $_helper->_getNewSelect();
        $_productLinesAlias = "productLines";
        $_customerOrdersAlias = "customerOrders";
        $_inventoryAlias = "inventory";
        $_grpByProductSelect->from([ $_productLinesAlias => $_productLinesSelect ],
            [ 'product_id' => 'product_id',
                'derived_name' => "line_name",
                'derived_sku' => 'line_sku',
                'derived_id' => "(if($_productLinesAlias.tree_name is not null, concat('T-', $_productLinesAlias.tree_id), if($_productLinesAlias.piece_name is not null, concat('L-', $_productLinesAlias.piece_id), concat('P-', $_productLinesAlias.product_id))))",
            ])
            ->joinLeft([ $_customerOrdersAlias => $_customerOrders ],
                "$_customerOrdersAlias.product_id = $_productLinesAlias.product_id",
                [ "total_qty_ordered",
                    "time_in_days" ])
            ->joinLeft([ $_inventoryAlias => $_inventory ],
                "$_inventoryAlias.product_id = $_productLinesAlias.product_id",
                [ 'qty',
                    'suppliers',
                    'purchase_orders',
                    'received_qty' => "ifnull(received_qty, 0)",
                    'incoming_qty' => "ifnull(incoming_qty, 0)",
                    'total_qty' => "ifnull(total_qty, 0)" ]);
        $this->log("\n\n2:\n".$_grpByProductSelect->__toString());

        $_lowStockRawSelect = $_helper->_getNewSelect();
        $_lowStockRaw = "lowStockRaw";
        $_lowStockRawSelect
            ->from([ $_lowStockRaw => $_grpByProductSelect ],
                [ 'derived_id',
                    'entity_id' => 'product_id',
                    'derived_name',
                    'derived_sku',
                    'total_qty_stock' => 'sum(ifnull(qty, 0))',
                    'total_qty_stock_and_transit' => 'sum(ifnull(qty, 0)) + sum(incoming_qty)',
                    'total_qty_sold' => "sum(ifnull(total_qty_ordered, 0))",
                    'time_in_days' => 'max(time_in_days)',
                    'suppliers',
                    'purchase_orders' => 'group_concat(purchase_orders)'
                ])
            ->group('derived_id');
        $this->log("\n\n3:\n" . $_lowStockRawSelect->__toString());

        $lowStockCalculated = self::TOP_LEVEL_TABLE_ALIAS;
        $_select
            ->from([ $lowStockCalculated => $_lowStockRawSelect ],
                [ 'derived_id',
                    'entity_id',
                    'derived_name',
                    'derived_sku',
                    'total_qty_stock',
                    'total_qty_stock_and_transit',
                    'total_qty_sold',
                    'time_in_days',
                    'purchase_orders',
                    'suppliers',
                    'weekly_rate' => "(7 * total_qty_sold / time_in_days)",
                    'estimated_remaining_weeks' => "(total_qty_stock_and_transit / (7 * total_qty_sold / time_in_days))"
                ])
            ->joinLeft([ 'catalog' => $this->getTable('catalog/product') ],
                "catalog.entity_id = $lowStockCalculated.entity_id",
                [])
            ->joinLeft([ 'attrset' => $this->getTable("eav/attribute_set") ],
                "catalog.attribute_set_id = attrset.attribute_set_id",
                "attribute_set_name")
            ->where("time_in_days is not null")
            ->where("catalog.type_id = 'simple'")
            ->where('attribute_set_name not in("Internal Use", "TRS-ZHacks")');

        $this->log("LowStockAvailability+inTransit SQL:\n".$_select->__toString());
    }

}