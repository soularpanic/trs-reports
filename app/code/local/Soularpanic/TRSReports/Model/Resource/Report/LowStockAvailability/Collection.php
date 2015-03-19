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

        $_qtySold = "ifnull(sum(qty_invoiced), 0)";
        $_startDate = "if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at)";
        $_endDate = "'{$this->_to}'";
        $_elapsedDays = "TIMESTAMPDIFF(DAY, {$_startDate}, {$_endDate})";
        $_weeklyRate = "(7 * total_qty_ordered / time_in_days)";
        $_availableQty = "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)";
        $_remainingWeeks = "if($_weeklyRate = 0, 99999, if(available_qty < 1, 0, ((available_qty) / ($_weeklyRate))))";


        $_select = $this->getSelect();


        $_customerOrderSelectAlias = 'customer_orders';
        $_customerOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_customerOrderSelect->from($_productTable,
            [ 'product_id' => 'entity_id',
                'sku' => 'sku' ])
            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_line_link') ],
                "line_links.product_id = {$_productTable}.entity_id",
                [ ])
            ->joinLeft([ 'lines' => $this->getTable('trsreports/product_line') ],
                'lines.entity_id = line_links.line_id',
                [ 'line_sku'            => 'line_sku',
                    'line_name'         => 'name',
                    'derived_sku'       => "(ifnull(lines.line_sku, {$_productTable}.sku))",
                    'derived_id'        => "(if(lines.entity_id is not null, concat('L-', lines.entity_id), concat('P-', {$_productTable}.entity_id)))",
                    'is_product_line'   => "(if(lines.line_sku is not null, TRUE, FALSE))"
                ])
            ->joinLeft($_productNameTable,
                "{$_productNameTable}.attribute_id = '{$productName->getId()}' and {$_productNameTable}.entity_id = {$_productTable}.entity_id",
                [ 'product_name' => "{$_productNameTable}.value",
                    'name' => "ifnull(lines.name, {$_productNameTable}.value)" ])
            ->joinLeft($_orderTable,
                "{$_orderTable}.product_id = {$_productTable}.entity_id and {$_orderTable}.created_at between '{$this->_from}' and '{$this->_to}'",
                [ 'period'              => 'created_at',
                    'total_qty_ordered' => $_qtySold,
                    'time_in_days'      => $_elapsedDays ])
            ->joinLeft($_stockTable,
                "{$_orderTable}.product_id = {$_stockTable}.product_id",
                [ 'available_qty' => "ifnull({$_availableQty}, 0)" ])
            ->group("derived_id");


        $_productSuppliers = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_productSuppliers
            ->from(['pop' => $this->getTable('Purchase/OrderProduct')],
                ['product_id' => 'pop_product_id'])
            ->joinLeft(['po' => $this->getTable('Purchase/Order')],
                "pop.pop_order_num = po.po_num",
                [])
            ->joinLeft(['ps' => $this->getTable('Purchase/Supplier')],
                "po.po_sup_num = ps.sup_id",
                [ 'suppliers' => "(GROUP_CONCAT(DISTINCT ps.sup_name ORDER BY ps.sup_name ASC))"])
            ->group('pop.pop_product_id');

        $_purchaseOrderData = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_purchaseOrderData
            ->from([ 'pps' => $this->getTable('Purchase/ProductSupplier') ],
                [ 'pps_product_id' ])
            ->joinLeft([ 'ps' => $this->getTable('Purchase/Supplier') ],
                "ps.sup_id = pps.pps_supplier_num",
                [])
            #[ 'sup_name' ])
            ->joinLeft( ['pop' => $this->getTable('Purchase/OrderProduct') ],
                "pop.pop_product_id = pps.pps_product_id AND pop.pop_supplied_qty < pop.pop_qty",
                [ 'pop_supplied_qty'    => "ifnull(pop_supplied_qty, 0)",
                    'pop_qty'           => "ifnull(pop_qty, 0)" ])
            ->joinLeft( [ 'po' => $this->getTable('Purchase/Order') ],
                "po.po_num = pop.pop_order_num AND po.po_status in ('waiting_for_delivery')",
                [ 'po_string' => "if(po.po_num is null, null, concat_ws('::', po.po_num, ps.sup_name, po.po_order_id, po.po_supply_date))" ]);


        $_purchaseOrdersByProduct = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_purchaseOrdersByProduct
            ->from([ 'po_data' => $_purchaseOrderData ],
                [ 'product_id'      => 'pps_product_id',
                    'incoming_qty'  => "sum(po_data.pop_qty) - sum(po_data.pop_supplied_qty)",
                    'encoded_pos'   => "concat_ws(',', po_data.po_string)",
                ])
            #'suppliers'     => "concat_ws(', ', po_data.sup_name)" ])
            ->group("po_data.pps_product_id");


        $_purchaseOrderSelectAlias = 'purchase_orders';
        $_purchaseOrderSelect = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $_purchaseOrderSelect
            ->from(['suppliers' => $_productSuppliers],
                ['product_id', 'suppliers'])
            ->joinLeft([ 'pobp' => $_purchaseOrdersByProduct ],
                "suppliers.product_id = pobp.product_id",
                #[ 'product_id',
                [
                    'incoming_qty' => "sum(pobp.incoming_qty)",
                    'encoded_pos',
                ])
            #   'suppliers' ])
            ->joinleft([ 'line_links' => $this->getTable('trsreports/product_line_link') ],
                "line_links.product_id = suppliers.product_id",
                [ 'derived_id' => "(if(line_links.line_id is not null, concat('L-', line_links.line_id), concat('P-', suppliers.product_id)))" ])
            ->group('derived_id');



        $_select->from($_productTable,
            [ 'entity_id',
                'sku',
                'rate' => $_weeklyRate,
                'remaining_stock_weeks' => $_remainingWeeks ])
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                [ 'attribute_set_name' => 'attribute_set_name' ])
            ->joinLeft([ $_customerOrderSelectAlias => $_customerOrderSelect ],
                "{$_customerOrderSelectAlias}.product_id = {$_productTable}.entity_id",
                '*')
            ->joinLeft([ $_purchaseOrderSelectAlias => $_purchaseOrderSelect ],
                "{$_purchaseOrderSelectAlias}.derived_id = {$_customerOrderSelectAlias}.derived_id",
                ['incoming_qty', 'encoded_pos', 'suppliers' ])
            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
            ->where('type_id = "simple"')
            ->where("{$_customerOrderSelectAlias}.derived_id is not null")
            ->where("{$_productTable}.sku is not null");

        $this->log("LowStockAvailability SQL:\n".$_select->__toString());
    }

    protected function _applyDateRangeFilter() {
        return $this;
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

}