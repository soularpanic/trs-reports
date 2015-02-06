<?php
class Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailabilityPlusTransit_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'sales/order_item';

    protected $_selectedColumns    = array();
    protected $_defaultSort = 'remaining_stock_weeks';

    protected function _initSelect()
    {
        $_orderTable = $this->getResource()->getMainTable();
        $_stockTable = 'cataloginventory_stock_item';
        $_productSupplierTable = 'purchase_product_supplier';
        $_supplierTable = 'purchase_supplier';

        $_productTable = 'catalog_product_entity';
        $_attributeSetTable = 'eav_attribute_set';
        $_purchaseOrderItemsTable = 'purchase_order_product';
        $_purchaseOrderTable = 'purchase_order';
        $this->getSelect()->from($_orderTable,
            array('name',
                'period' => 'created_at',
                'total_qty_ordered' => "sum(qty_ordered)",
                'time' => "TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')",
                'rate' => "7 * sum(qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')"))
            ->where("product_type = 'simple'")
            ->joinLeft($_productTable,
                "{$_orderTable}.product_id = {$_productTable}.entity_id",
                array('sku'))
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                array('attribute_set_name'))
            ->joinLeft($_stockTable,
                "{$_orderTable}.product_id = {$_stockTable}.product_id",
                array('available_qty' => "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)"))
            ->joinLeft($_purchaseOrderItemsTable,
                "{$_purchaseOrderItemsTable}.pop_product_id = {$_stockTable}.product_id and {$_purchaseOrderItemsTable}.pop_supplied_qty < {$_purchaseOrderItemsTable}.pop_qty",
                array('incoming_qty' => "ifnull({$_purchaseOrderItemsTable}.pop_qty, 0)",
                    'total_qty' => "(ifnull({$_purchaseOrderItemsTable}.pop_qty, 0) + {$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)",
                    'remaining_stock_weeks' => "(ifnull({$_purchaseOrderItemsTable}.pop_qty, 0) + {$_stockTable}.qty - {$_stockTable}.stock_reserved_qty) / (7 * sum({$_orderTable}.qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}'))"))
            ->joinLeft($_purchaseOrderTable,
                "{$_purchaseOrderItemsTable}.pop_order_num = {$_purchaseOrderTable}.po_num and {$_purchaseOrderTable}.po_status in('new', 'waiting_for_delivery')",
                array(
                    "po_id" => "po_num",
                    "po_number" => "po_order_id",
                    "po_supply_date"))
//            ->joinLeft($_productSupplierTable,
//                "{$_orderTable}.product_id = {$_productSupplierTable}.pps_product_id",
//                array())
            ->joinLeft(array('sup1' => $_supplierTable),
                "sup1.sup_id = {$_purchaseOrderTable}.po_sup_num",
                array('supplier_name' => 'sup1.sup_name'))
//            ->joinLeft(array('sup2' => $_supplierTable),
//                "sup2.sup_id = {$_productSupplierTable}.pps_supplier_num",
//                array('supplier_name' => "(ifnull(sup2.sup_name, sup1.sup_name))"))

            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
            ->group("{$_orderTable}.product_id");
        $this->log("SQL:\n".$this->getSelect()->__toString());
    }

    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases including Oracle
        if ($this->_from !== null) {
            $this->getSelect()->where("{$this->getResource()->getMainTable()}.created_at >= ?", $this->_from);
        }
        if ($this->_to !== null) {
            $this->getSelect()->where("{$this->getResource()->getMainTable()}.created_at <= ?", $this->_to);
        }

        return $this;
    }


}