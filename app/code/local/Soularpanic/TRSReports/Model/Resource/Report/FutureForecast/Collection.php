<?php
class Soularpanic_TRSReports_Model_Resource_Report_FutureForecast_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    const TOP_LEVEL_TABLE_ALIAS = 'boom';

    protected $_aggregationTable = 'sales/order_item';

    protected $_selectedColumns    = [];
    protected $_defaultSort = 'estimated_remaining_weeks';

    protected function _initSelect() {

        $filterData = $this->getCustomFilterData();

        $growth = $filterData->getGrowthPercent();
        $futureRate = $growth ? 1.0 + (floatval($growth) / 100.0) : 1.0;
//        if (!$growth) {
//            $growth = "0";
//        }
        $futureDate = $filterData->getFuture();
        $futureDate = $futureDate ? "'$futureDate'" : "NOW()";
//        if (!$futureDate) {
//            $futureDate = "NOW()";
//        }
//        else {
//            $futureDate = "'$futureDate'";
//        }

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
                    'total_qty' => "ifnull(total_qty, 0)"]);
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
        $_qtyToOrderFormula = "(($futureRate * DATEDIFF($futureDate, NOW()) * total_qty_sold / time_in_days) - total_qty_stock_and_transit)";
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
                    'estimated_remaining_weeks' => "(total_qty_stock / (7 * total_qty_sold / time_in_days))",
                    'future_qty' => "($futureRate * DATEDIFF($futureDate, NOW()) * total_qty_sold / time_in_days)",
                    "qty_to_order" => "if($_qtyToOrderFormula > 0, $_qtyToOrderFormula, 0)"
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

//        $this->log("LowStockAvailability SQL:\n".$_select->__toString());
//
//        $_orderTable = $this->getResource()->getMainTable();
//        $_stockTable = 'cataloginventory_stock_item';
//        $_productSupplierTable = 'purchase_product_supplier';
//        $_supplierTable = 'purchase_supplier';
//        $_purchaseOrderItemsTable = 'purchase_order_product';
//        $_purchaseOrderTable = 'purchase_order';
//        $_productTable = $this->getProductTable(); //'catalog_product_entity';
//        $_attributeSetTable = 'eav_attribute_set';
//
//        $filterData = $this->getCustomFilterData();
//
//        $growth = $filterData->getGrowthPercent();
//        if (!$growth) {
//            $growth = "0";
//        }
//        $futureDate = $filterData->getFuture();
//        if (!$futureDate) {
//            $futureDate = "NOW()";
//        }
//        else {
//            $futureDate = "'$futureDate'";
//        }
//
//        $this->getSelect()->from($_orderTable,
//            [ 'sku',
//                'name',
//                'period' => 'created_at',
//                'total_qty_ordered' => "sum(qty_ordered)",
//                'time' => "TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')",
//                'rate' => "sum(qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')",
//                'time_to_future' => "TIMESTAMPDIFF(DAY, NOW(), {$futureDate})",
//                'future_qty' => "TIMESTAMPDIFF(DAY, NOW(), {$futureDate}) * (1.0 + {$growth}/100) * (sum(qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}'))"])
//            ->where("product_type = 'simple'")
//            ->joinLeft($_stockTable,
//                "{$_orderTable}.product_id = {$_stockTable}.product_id",
//                [ 'available_qty' => "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)",
//                    'remaining_stock_weeks' => "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty) / (7 * sum({$_orderTable}.qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}'))"])
//            ->joinLeft($_productSupplierTable,
//                "{$_orderTable}.product_id = {$_productSupplierTable}.pps_product_id",
//                [])
//            ->joinLeft($_supplierTable,
//                "{$_supplierTable}.sup_id = {$_productSupplierTable}.pps_supplier_num",
//                ['supplier_name' => 'sup_name'])
//            ->joinLeft($_purchaseOrderItemsTable,
//                "{$_purchaseOrderItemsTable}.pop_product_id = {$_stockTable}.product_id and {$_purchaseOrderItemsTable}.pop_supplied_qty < {$_purchaseOrderItemsTable}.pop_qty",
//                [ 'qty_incoming' => "ifnull({$_purchaseOrderItemsTable}.pop_qty, 0)" ])
//            ->joinLeft($_purchaseOrderTable,
//                "{$_purchaseOrderItemsTable}.pop_order_num = {$_purchaseOrderTable}.po_num and {$_purchaseOrderTable}.po_status in('new', 'waiting_for_delivery')",
//                [ "po_id" => "po_num",
//                    "po_number" => "po_order_id",
//                    "po_supply_date" ])
//            ->joinLeft($_productTable,
//                "{$_orderTable}.product_id = {$_productTable}.entity_id",
//                [ ])
//            ->joinLeft($_attributeSetTable,
//                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
//                [ 'attribute_set_name' ])
//            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
//            ->group("{$_orderTable}.product_id");

        $this->log('Future Forecast SQL:\n'.$this->getSelect()->__toString());
    }

    protected function _applyCustomFilter() {
        $customFilterData = $this->getCustomFilterData();
        $customFilterData->setProductTable(self::TOP_LEVEL_TABLE_ALIAS);

        return parent::_applyCustomFilter();
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter() {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases including Oracle
//        if ($this->_from !== null) {
//            $this->getSelect()->where("{$this->getResource()->getMainTable()}.created_at >= ?", $this->_from);
//        }
//        if ($this->_to !== null) {
//            $this->getSelect()->where("{$this->getResource()->getMainTable()}.created_at <= ?", $this->_to);
//        }

        return $this;
    }


}