<?php
class Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailability_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'sales/order_item';

    protected $_selectedColumns    = array();
    protected $_defaultSort = 'remaining_stock_weeks';

    protected function _initSelect() {
        $productName = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');

        $_orderTable = $this->getResource()->getMainTable();
        $_stockTable = 'cataloginventory_stock_item';
        $_productTable = $this->getProductTable();
        $_productNameTable = $productName->getBackendTable();
        $_attributeSetTable = 'eav_attribute_set';

        $_supplierSql = Mage::helper('trsreports/purchaseOrders')->getPurchaseOrdersByProductSql();

        $_qtySold = "sum(qty_invoiced)";
        $_startDate = "if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at)";
        $_endDate = "'{$this->_to}'";
        $_elapsedDays = "TIMESTAMPDIFF(DAY, {$_startDate}, {$_endDate})";
        $_weeklyRate = "(7 * {$_qtySold} / {$_elapsedDays})";
        $_availableQty = "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)";

        $_select = $this->getSelect();


        $_customerOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_purchaseOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
//        $_purchaseOrderSelect->from(new Zend_Db_Expr($_supplierSql));
//        $_purchaseOrderSelect
//            ->joinLeft ([ 'suppliers' => new Zend_Db_Expr($_supplierSql) ],
//            "suppliers.product_id = {$_productTable}.entity_id",
//            [ 'supplier_names' => 'suppliers',
//                'incoming_qty' => 'ifnull(incoming_qty, 0)'])



//
//        $_customerOrderSelect = clone $_select;
//        $_customerOrderSelect->from($_productTable,
//            [ 'entity_id' => 'entity_id',
//                'sku' => 'sku' ])
//            ->where('type_id = "simple"')
//            ->joinLeft($_productNameTable,
//                "{$_productNameTable}.attribute_id = '{$productName->getId()}' and {$_productNameTable}.entity_id = {$_productTable}.entity_id",
//                [ ])
//            ->joinLeft($_attributeSetTable,
//                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
//                [ 'attribute_set_name' => 'attribute_set_name' ])
//            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_line_link') ],
//                "line_links.product_id = {$_productTable}.entity_id",
//                [ ])
//            ->joinLeft([ 'lines' => $this->getTable('trsreports/product_line') ],
//                'lines.entity_id = line_links.line_id',
//                [ 'line_sku'        => 'line_sku',
//                    'name'          => "(ifnull(lines.name, {$_productNameTable}.value))",
//                    'derived_sku'   => "(ifnull(lines.line_sku, {$_productTable}.sku))",
//                    'derived_id'    => "(if(lines.entity_id is not null, concat('L-', lines.entity_id), concat('P-', {$_productTable}.entity_id)))",
//                    'is_product_line' => "(if(lines.line_sku is not null, TRUE, FALSE))"
//                ])
//            ->joinLeft($_orderTable,
//                "{$_orderTable}.product_id = {$_productTable}.entity_id and {$_orderTable}.created_at between '{$this->_from}' and '{$this->_to}'",
//                [ 'period'              => 'created_at',
//                    'total_qty_ordered' => $_qtySold,
//                    'time'              => $_elapsedDays,
//                    'rate'              => $_weeklyRate ])
//            ->joinLeft($_stockTable,
//                "{$_orderTable}.product_id = {$_stockTable}.product_id",
//                [ 'available_qty'           => "ifnull({$_availableQty}, 0)",
//                    'remaining_stock_weeks' => "{$_availableQty} / {$_weeklyRate}" ])
//            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")');
////            ->group("{$_productTable}.entity_id");
////            ->group("derived_id");
//
//
//
//        $_purchaseOrderSelect = clone $_select;
//        $_purchaseOrderSelect->from([ 'suppliers' => new Zend_Db_Expr($_supplierSql) ],
////            "suppliers.product_id = {$_productTable}.entity_id",
//            [ 'product_id' => 'product_id',
//                'supplier_names' => 'suppliers',
//                'incoming_qty' => 'ifnull(incoming_qty, 0)']);
//
//        $_select->from(['customer_orders' => $_customerOrderSelect],
//            '*')
//            ->joinLeft([ 'purchase_orders' => $_purchaseOrderSelect ],
//                'customer_orders.entity_id = purchase_orders.product_id',
//                '*');

        $_customerOrderSelect->from($_productTable,
            [ 'product_id' => 'entity_id',
                'sku' => 'sku' ])
            ->where('type_id = "simple"')
            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_line_link') ],
                "line_links.product_id = {$_productTable}.entity_id",
                [ ])
            ->joinLeft([ 'lines' => $this->getTable('trsreports/product_line') ],
                'lines.entity_id = line_links.line_id',
                [ 'line_sku'        => 'line_sku',
                    'line_name'          => 'name', #"(ifnull(lines.name, {$_productNameTable}.value))",
                    'derived_sku'   => /* 'line_sku', */ "(ifnull(lines.line_sku, {$_productTable}.sku))",
                    'derived_id'    => "(if(lines.entity_id is not null, concat('L-', lines.entity_id), concat('P-', {$_productTable}.entity_id)))",
                    'is_product_line' => "(if(lines.line_sku is not null, TRUE, FALSE))"
                ])
            ->joinLeft($_orderTable,
                "{$_orderTable}.product_id = {$_productTable}.entity_id and {$_orderTable}.created_at between '{$this->_from}' and '{$this->_to}'",
                [ 'period'              => 'created_at',
                    'total_qty_ordered' => $_qtySold,
                    'time'              => $_elapsedDays,
                    #    'rate'              => $_weeklyRate
                ])
            ->joinLeft($_stockTable,
                "{$_orderTable}.product_id = {$_stockTable}.product_id",
                [ 'available_qty'           => "ifnull({$_availableQty}, 0)",
                    #'remaining_stock_weeks' => "{$_availableQty} / {$_weeklyRate}"
                ])

            ->group("product_id");



        $_purchaseOrderData = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_purchaseOrderData
            ->from([ 'pps' => $this->getTable('Purchase/ProductSupplier') ],
                [ 'pps_product_id' ])
            ->joinLeft([ 'ps' => $this->getTable('Purchase/Supplier') ],
                "ps.sup_id = pps.pps_supplier_num",
                [ 'sup_name' ])
            ->joinLeft( ['pop' => $this->getTable('Purchase/OrderProduct') ],
                "pop.pop_product_id = pps.pps_product_id AND pop.pop_supplied_qty < pop.pop_qty",
                [ 'pop_supplied_qty',
                    'pop_qty' ])
            ->joinLeft( [ 'po' => $this->getTable('Purchase/Order') ],
                "po.po_num = pop.pop_order_num AND po.po_status in ('new', 'waiting_for_delivery')",
                [ 'po_string' => "concat_ws('::', po.po_num, ps.sup_name, po.po_order_id, po.po_supply_date)" ])
            ->where('po.po_num is not null');

        $_purchaseOrdersByProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_purchaseOrdersByProduct
            ->from([ 'po_data' => $_purchaseOrderData ],
                [ 'product_id' => 'pps_product_id',
                    'incoming_qty' => "sum(po_data.pop_qty) - sum(po_data.pop_supplied_qty)",
                    'encoded_pos' => "concat_ws(',', po_data.po_string)",
                    'suppliers' => "concat_ws(', ', po_data.sup_name)" ])
            ->group("po_data.pps_product_id");

        //$_purchaseOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_purchaseOrderSelect
            ->from([ 'pobp' => $_purchaseOrdersByProduct ],
                ['product_id',
                    'incoming_qty' => "sum(pobp.incoming_qty)",
                    'encoded_pos',
                    'suppliers'
                ])
            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_line_link') ],
                "line_links.product_id = pobp.product_id",
                [ 'derived_id' => "(if(line_links.line_id is not null, concat('L-', line_links.line_id), concat('P-', pobp.product_id)))"])
            ->group('derived_id');


        $_customerOrderSelectAlias = 'customer_orders';
        $_purchaseOrderSelectAlias = 'purchase_orders';
        $_select->from($_productTable,
            [ 'entity_id',
                'sku',
                'remaining_stock_weeks' => '(9001)' ])
            ->joinLeft($_productNameTable,
                "{$_productNameTable}.attribute_id = '{$productName->getId()}' and {$_productNameTable}.entity_id = {$_productTable}.entity_id",
                [ 'name' => "ifnull({$_customerOrderSelectAlias}.line_name, {$_productNameTable}.value)" ])
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                [ 'attribute_set_name' => 'attribute_set_name' ])
            ->joinLeft([ $_customerOrderSelectAlias => $_customerOrderSelect ],
                "{$_customerOrderSelectAlias}.product_id = {$_productTable}.entity_id",
                '*')
            ->joinLeft([ $_purchaseOrderSelectAlias => $_purchaseOrderSelect ], #new Zend_Db_Expr($_supplierSql) ],
                "{$_purchaseOrderSelectAlias}.product_id = {$_productTable}.entity_id",
                '*')
//            ->joinLeft([ $_purchaseOrderSelectAlias => $_purchaseOrderSelect],
//                "{$_purchaseOrderSelectAlias}.product_id = $_productTable.entity_id",
//                '*')
            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")');

        $this->log("LowStockAvailability SQL:\n".$_select->__toString());
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

}