<?php
class Soularpanic_TRSReports_Model_Resource_Report_FutureForecast_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'sales/order_item';

    protected $_selectedColumns    = [];
    protected $_defaultSort = 'remaining_stock_weeks';

    protected function _initSelect() {
        $_orderTable = $this->getResource()->getMainTable();
        $_stockTable = 'cataloginventory_stock_item';
        $_productSupplierTable = 'purchase_product_supplier';
        $_supplierTable = 'purchase_supplier';
        $_purchaseOrderItemsTable = 'purchase_order_product';
        $_purchaseOrderTable = 'purchase_order';
        $_productTable = $this->getProductTable(); //'catalog_product_entity';
        $_attributeSetTable = 'eav_attribute_set';

        $filterData = $this->getCustomFilterData();

        $growth = $filterData->getGrowthPercent();
        if (!$growth) {
            $growth = "0";
        }
        $futureDate = $filterData->getFuture();
        if (!$futureDate) {
            $futureDate = "NOW()";
        }
        else {
            $futureDate = "'$futureDate'";
        }

        $this->getSelect()->from($_orderTable,
            [ 'sku',
                'name',
                'period' => 'created_at',
                'total_qty_ordered' => "sum(qty_ordered)",
                'time' => "TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')",
                'rate' => "sum(qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')",
                'time_to_future' => "TIMESTAMPDIFF(DAY, NOW(), {$futureDate})",
                'future_qty' => "TIMESTAMPDIFF(DAY, NOW(), {$futureDate}) * (1.0 + {$growth}/100) * (sum(qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}'))"])
            ->where("product_type = 'simple'")
            ->joinLeft($_stockTable,
                "{$_orderTable}.product_id = {$_stockTable}.product_id",
                [ 'available_qty' => "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)",
                    'remaining_stock_weeks' => "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty) / (7 * sum({$_orderTable}.qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}'))"])
            ->joinLeft($_productSupplierTable,
                "{$_orderTable}.product_id = {$_productSupplierTable}.pps_product_id",
                [])
            ->joinLeft($_supplierTable,
                "{$_supplierTable}.sup_id = {$_productSupplierTable}.pps_supplier_num",
                ['supplier_name' => 'sup_name'])
            ->joinLeft($_purchaseOrderItemsTable,
                "{$_purchaseOrderItemsTable}.pop_product_id = {$_stockTable}.product_id and {$_purchaseOrderItemsTable}.pop_supplied_qty < {$_purchaseOrderItemsTable}.pop_qty",
                [ 'qty_incoming' => "ifnull({$_purchaseOrderItemsTable}.pop_qty, 0)" ])
//                    'total_qty' => "(ifnull({$_purchaseOrderItemsTable}.pop_qty, 0) + {$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)",
//                    'remaining_stock_weeks' => "(ifnull({$_purchaseOrderItemsTable}.pop_qty, 0) + {$_stockTable}.qty - {$_stockTable}.stock_reserved_qty) / (7 * sum({$_orderTable}.qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}'))"))
            ->joinLeft($_purchaseOrderTable,
                "{$_purchaseOrderItemsTable}.pop_order_num = {$_purchaseOrderTable}.po_num and {$_purchaseOrderTable}.po_status in('new', 'waiting_for_delivery')",
                [ "po_id" => "po_num",
                    "po_number" => "po_order_id",
                    "po_supply_date" ])
            ->joinLeft($_productTable,
                "{$_orderTable}.product_id = {$_productTable}.entity_id",
                [ ])
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                [ 'attribute_set_name' ])
            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
            ->group("{$_orderTable}.product_id");

        $this->log('Future Forecast SQL:\n'.$this->getSelect()->__toString());
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