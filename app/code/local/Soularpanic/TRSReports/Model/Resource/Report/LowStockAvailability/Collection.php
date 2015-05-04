<?php
class Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailability_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    const TOP_LEVEL_TABLE_ALIAS = "lowStockCalculated";

    protected $_aggregationTable = 'sales/order_item';

    protected $_selectedColumns    = array();
    protected $_defaultSort = 'estimated_remaining_weeks';

    protected function _initSelect() {
        $productName = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');

        $_orderTable = $this->getResource()->getMainTable();
        $_stockTable = 'cataloginventory_stock_item';
        $_productTable = $this->getProductTable();
        $_productNameTable = $productName->getBackendTable();
        $_attributeSetTable = 'eav_attribute_set';

        $_qtySold = "ifnull(sum(qty_invoiced), 0)";
        $_startDate = "if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at)";
        $_endDate = "'{$this->_to}'";
        $_elapsedDays = "TIMESTAMPDIFF(DAY, {$_startDate}, {$_endDate})";
        $_weeklyRate = "(7 * total_qty_ordered / time_in_days)";
        $_availableQty = "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)";
        $_remainingWeeks = "if($_weeklyRate = 0, 99999, if(available_qty < 1, 0, ((available_qty) / ($_weeklyRate))))";


        $_select = $this->getSelect();
        $_helper = Mage::helper('trsreports/collection');

        $_productLinesSelect = $_helper->getProductLinesSelect();
        $_customerOrders = $_helper->getProductOrders($this->_from, $this->_to, $_productLinesSelect);
        $_inventory = $_helper->getProductInventory($this->_from, $this->_to);

        $_select2 = $_helper->_getNewSelect();
        $_productLinesAlias = "productLines";
        $_customerOrdersAlias = "customerOrders";
        $_inventoryAlias = "inventory";
        $_select2->from([ $_productLinesAlias => $_productLinesSelect ],
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
                    'purchase_orders' ]);
        $this->log("\n\n2:\n".$_select2->__toString());

        $_lowStockRawSelect = $_helper->_getNewSelect();
        $_lowStockRaw = "lowStockRaw";
        $_lowStockRawSelect
            ->from([ $_lowStockRaw => $_select2 ],
                [ 'derived_id',
                    'entity_id' => 'product_id',
                    'derived_name',
                    'derived_sku',
                    'total_qty_stock' => 'sum(ifnull(qty, 0))',
                    'total_qty_sold' => "sum(ifnull(total_qty_ordered, 0))",
                    'time_in_days' => 'max(time_in_days)',
                    'suppliers',
                    'purchase_orders' => 'group_concat(purchase_orders)'
                ])
            ->group('derived_id');
        $this->log("\n\n3:\n" . $_lowStockRawSelect->__toString());

        $lowStockCalculated = self::TOP_LEVEL_TABLE_ALIAS;
//        $_select4 = $_helper->_getNewSelect();
//        $_select4
        $_select
            ->from([ $lowStockCalculated => $_lowStockRawSelect ],
                [ 'derived_id',
                    'entity_id',
                    'derived_name',
                    'derived_sku',
                    'total_qty_stock',
                    'total_qty_sold',
                    'time_in_days',
                    'purchase_orders',
                    'suppliers',
                    'weekly_rate' => "(7 * total_qty_sold / time_in_days)",
                    'estimated_remaining_weeks' => "(total_qty_stock / (7 * total_qty_sold / time_in_days))"
                ])
            ->joinLeft([ 'catalog' => $this->getTable('catalog/product') ],
                "catalog.entity_id = $lowStockCalculated.entity_id",
                [])
            ->joinLeft([ 'attrset' => $this->getTable("eav/attribute_set") ],
                "catalog.attribute_set_id = attrset.attribute_set_id",
                "attribute_set_name")
            ->where("time_in_days is not null")
            ->where("catalog.type_id = 'simple'")
            ->where('attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")');
//        $this->log("\n\n4:\n" . $_select4->__toString());
//    )

//        $_purchaseOrders = $_helper->getProductInventory();


//        $_lines = "lines";
//        $_customerOrderSelectAlias = 'customer_orders';
//        $_customerOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_customerOrderSelect->from($_productTable,
//            [ 'product_id' => 'entity_id',
//                'sku' => 'sku' ])
//            ->join([$_lines => $_productLinesSelect],
//                "$_lines.product_id = {$_productTable}.entity_id",
//                [ 'derived_sku' => "line_sku",
//                    'derived_id' => "(
//                        if($_lines.tree_name is not null,
//                            concat('T-', $_lines.tree_id),
//                            if($_lines.piece_name is not null,
//                                concat('L-', $_lines.piece_id),
//                                concat('P-', {$_productTable}.entity_id)
//                    )))",
//                    "line_type" => "(if($_lines.tree_name is not null, 'TREE', if($_lines.piece_name is not null, 'PIECE', 'PRODUCT')))"
//                ])
//            ->joinLeft($_productNameTable,
//                "{$_productNameTable}.attribute_id = '{$productName->getId()}' and {$_productNameTable}.entity_id = {$_productTable}.entity_id",
//                [ 'product_name' => "{$_productNameTable}.value",
//                    'name' => "ifnull(lines.line_name, {$_productNameTable}.value)" ])
//            ->joinLeft($_orderTable,
//                "{$_orderTable}.product_id = {$_productTable}.entity_id and {$_orderTable}.created_at between '{$this->_from}' and '{$this->_to}'",
//                [ 'period'              => 'created_at',
//                    'total_qty_ordered' => $_qtySold,
//                    'time_in_days'      => $_elapsedDays ])
//            ->joinLeft($_stockTable,
//                "{$_orderTable}.product_id = {$_stockTable}.product_id",
//                [ 'available_qty' => "ifnull({$_availableQty}, 0)" ])
//            ->group("derived_id");
//
//
//        $_productSuppliers = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_productSuppliers
//            ->from(['pop' => $this->getTable('Purchase/OrderProduct')],
//                ['product_id' => 'pop_product_id'])
//            ->joinLeft(['po' => $this->getTable('Purchase/Order')],
//                "pop.pop_order_num = po.po_num",
//                [])
//            ->joinLeft(['ps' => $this->getTable('Purchase/Supplier')],
//                "po.po_sup_num = ps.sup_id",
//                [ 'suppliers' => "(GROUP_CONCAT(DISTINCT ps.sup_name ORDER BY ps.sup_name ASC))"])
//            ->group('pop.pop_product_id');
//
//        $_purchaseOrderData = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_purchaseOrderData
//            ->from([ 'pps' => $this->getTable('Purchase/ProductSupplier') ],
//                [ 'pps_product_id' ])
//            ->joinLeft([ 'ps' => $this->getTable('Purchase/Supplier') ],
//                "ps.sup_id = pps.pps_supplier_num",
//                [])
//            ->joinLeft( ['pop' => $this->getTable('Purchase/OrderProduct') ],
//                "pop.pop_product_id = pps.pps_product_id AND pop.pop_supplied_qty < pop.pop_qty",
//                [ 'pop_supplied_qty'    => "ifnull(pop_supplied_qty, 0)",
//                    'pop_qty'           => "ifnull(pop_qty, 0)" ])
//            ->joinLeft( [ 'po' => $this->getTable('Purchase/Order') ],
//                "po.po_num = pop.pop_order_num AND po.po_status in ('waiting_for_delivery')",
//                [ 'po_string' => "if(po.po_num is null, null, concat_ws('::', po.po_num, ps.sup_name, po.po_order_id, po.po_supply_date))" ]);
//
//
//        $_purchaseOrdersByProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_purchaseOrdersByProduct
//            ->from([ 'po_data' => $_purchaseOrderData ],
//                [ 'product_id'      => 'pps_product_id',
//                    'incoming_qty'  => "sum(po_data.pop_qty) - sum(po_data.pop_supplied_qty)",
//                    'encoded_pos'   => "concat_ws(',', po_data.po_string)",
//                ])
//            ->group("po_data.pps_product_id");
//
//
//        $_purchaseOrderSelectAlias = 'purchase_orders';
//        $_purchaseOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_purchaseOrderSelect
//            ->from(['suppliers' => $_productSuppliers],
//                ['product_id', 'suppliers'])
//            ->joinLeft([ 'pobp' => $_purchaseOrdersByProduct ],
//                "suppliers.product_id = pobp.product_id",
//                [ 'incoming_qty' => "sum(pobp.incoming_qty)",
//                    'encoded_pos',
//                ])
//            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_piece_link') ],
//                "line_links.product_id = suppliers.product_id",
//                [ 'derived_id' => "(if(line_links.pieced_product_id is not null, concat('L-', line_links.pieced_product_id), concat('P-', suppliers.product_id)))" ])
//            ->group('derived_id');
//
//
//
//        $_select->from($_productTable,
//            [ 'entity_id',
//                'sku',
//                'rate' => $_weeklyRate,
//                'remaining_stock_weeks' => $_remainingWeeks ])
//            ->joinLeft($_attributeSetTable,
//                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
//                [ 'attribute_set_name' => 'attribute_set_name' ])
//            ->joinLeft([ $_customerOrderSelectAlias => $_customerOrderSelect ],
//                "{$_customerOrderSelectAlias}.product_id = {$_productTable}.entity_id",
//                '*')
//            ->joinLeft([ $_purchaseOrderSelectAlias => $_purchaseOrderSelect ],
//                "{$_purchaseOrderSelectAlias}.derived_id = {$_customerOrderSelectAlias}.derived_id",
//                ['incoming_qty', 'encoded_pos', 'suppliers' ])
//            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
//            ->where('type_id = "simple"')
//            ->where("{$_customerOrderSelectAlias}.derived_id is not null")
//            ->where("{$_productTable}.sku is not null");

        $this->log("LowStockAvailability SQL:\n".$_select->__toString());
    }

    protected function _applyCustomFilter()
    {
        $customFilterData = $this->getCustomFilterData();
        $customFilterData->setProductTable(self::TOP_LEVEL_TABLE_ALIAS);

        return parent::_applyCustomFilter(); // TODO: Change the autogenerated stub
    }


    protected function _applyDateRangeFilter() {
        return $this;
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

}