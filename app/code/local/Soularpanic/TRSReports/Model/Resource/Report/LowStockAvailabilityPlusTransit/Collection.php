<?php
class Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailabilityPlusTransit_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailability_Collection {
//    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

//    protected $_aggregationTable = 'sales/order_item';
//
//    protected $_selectedColumns    = array();
//    protected $_defaultSort = 'remaining_stock_weeks';

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
                ->where('attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")');

            $this->log("LowStockAvailability+inTransit SQL:\n".$_select->__toString());


//        $productName = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');
//
//        $_orderTable = $this->getResource()->getMainTable();
//        $_stockTable = 'cataloginventory_stock_item';
//        $_productTable = $this->getProductTable();
//        $_productNameTable = $productName->getBackendTable();
//        $_attributeSetTable = 'eav_attribute_set';
//
//        $_customerOrderSelectAlias = 'customer_orders';
//        $_purchaseOrderSelectAlias = 'purchase_orders';
//
//        $_qtySold = "ifnull(sum(qty_invoiced), 0)";
//        $_startDate = "if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at)";
//        $_endDate = "'{$this->_to}'";
//        $_elapsedDays = "TIMESTAMPDIFF(DAY, {$_startDate}, {$_endDate})";
//        $_weeklyRate = "(7 * total_qty_ordered / time_in_days)";
//        $_availableQty = "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)";
//        $_availablePlusInTransitQty = "({$_customerOrderSelectAlias}.available_qty + ifnull({$_purchaseOrderSelectAlias}.incoming_qty, 0))";
//        $_remainingWeeks = "if($_weeklyRate = 0, 99999, if($_availablePlusInTransitQty < 1, 0, (($_availablePlusInTransitQty) / ($_weeklyRate))))";
//
//
//        $_select = $this->getSelect();
//
//
//        $_customerOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_customerOrderSelect->from($_productTable,
//            [ 'product_id' => 'entity_id',
//                'sku' => 'sku' ])
//            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_piece_link') ],
//                "line_links.product_id = {$_productTable}.entity_id",
//                [ ])
//            ->joinLeft([ 'lines' => $this->getTable('trsreports/product_piece_product') ],
//                'lines.entity_id = line_links.pieced_product_id',
//                [ 'line_sku'            => 'pieced_product_sku',
//                    'line_name'         => 'name',
//                    'derived_sku'       => "(ifnull(lines.pieced_product_sku, {$_productTable}.sku))",
//                    'derived_id'        => "(if(lines.entity_id is not null, concat('L-', lines.entity_id), concat('P-', {$_productTable}.entity_id)))",
//                    'is_product_line'   => "(if(lines.pieced_product_sku is not null, TRUE, FALSE))"
//                ])
//            ->joinLeft($_productNameTable,
//                "{$_productNameTable}.attribute_id = '{$productName->getId()}' and {$_productNameTable}.entity_id = {$_productTable}.entity_id",
//                [ 'product_name' => "{$_productNameTable}.value",
//                    'name' => "ifnull(lines.name, {$_productNameTable}.value)" ])
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
//        $_purchaseOrderData = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_purchaseOrderData
//            ->from([ 'pps' => $this->getTable('Purchase/ProductSupplier') ],
//                [ 'pps_product_id' ])
//            ->joinLeft([ 'ps' => $this->getTable('Purchase/Supplier') ],
//                "ps.sup_id = pps.pps_supplier_num",
//                [ 'sup_name' ])
//            ->joinLeft( ['pop' => $this->getTable('Purchase/OrderProduct') ],
//                "pop.pop_product_id = pps.pps_product_id AND pop.pop_supplied_qty < pop.pop_qty",
//                [ 'pop_supplied_qty'    => "ifnull(pop_supplied_qty, 0)",
//                    'pop_qty'           => "ifnull(pop_qty, 0)" ])
//            ->joinLeft( [ 'po' => $this->getTable('Purchase/Order') ],
//                "po.po_num = pop.pop_order_num AND po.po_status in ('new', 'waiting_for_delivery')",
//                [ 'expected_delivery_date' => 'po.po_supply_date',
//                    'po_string' => "if(po.po_num is null, null, concat_ws('::', po.po_num, ps.sup_name, po.po_order_id, po.po_supply_date))" ]);
//
//
//        $_purchaseOrdersByProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_purchaseOrdersByProduct
//            ->from([ 'po_data' => $_purchaseOrderData ],
//                [ 'product_id'      => 'pps_product_id',
//                    'incoming_qty'  => "sum(po_data.pop_qty) - sum(po_data.pop_supplied_qty)",
//                    'encoded_pos'   => "concat_ws(',', po_data.po_string)",
//                    'suppliers'     => "concat_ws(', ', po_data.sup_name)",
//                    'expected_delivery_date' ])
//            ->group("po_data.pps_product_id");
//
//
//
//        $_purchaseOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_purchaseOrderSelect
//            ->from([ 'pobp' => $_purchaseOrdersByProduct ],
//                [ 'product_id',
//                    'incoming_qty' => "ifnull(sum(pobp.incoming_qty), 0)",
//                    'expected_delivery_date',
//                    'encoded_pos',
//                    'suppliers' ])
//            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_piece_link') ],
//                "line_links.product_id = pobp.product_id",
//                [ 'derived_id' => "(if(line_links.pieced_product_id is not null, concat('L-', line_links.pieced_product_id), concat('P-', pobp.product_id)))" ])
//            ->group('derived_id');
//
//
//
//        $_select->from($_productTable,
//            [ 'entity_id',
//                'sku',
//                'total_qty' => $_availablePlusInTransitQty,
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
//                [ 'incoming_qty' => "ifnull(incoming_qty, 0)",
//
//                    '*'])
//            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
//            ->where('type_id = "simple"')
//            ->where("{$_customerOrderSelectAlias}.derived_id is not null")
//            ->where("{$_productTable}.sku is not null");
//
//        $this->log("Low Stock Availability + in Transit SQL:\n".$this->getSelect()->__toString());
    }

//    protected function _applyDateRangeFilter() {
//        return $this;
//    }
//
//    protected function _applyStoresFilter()
//    {
//        return $this;
//    }

}