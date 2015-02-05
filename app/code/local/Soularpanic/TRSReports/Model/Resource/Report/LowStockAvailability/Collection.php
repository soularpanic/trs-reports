<?php
class Soularpanic_TRSReports_Model_Resource_Report_LowStockAvailability_Collection
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
        $_productVarcharTable = 'catalog_product_entity_varchar';
        $_attributeSetTable = 'eav_attribute_set';

        $_select = $this->getSelect();
        $_select->from($_productTable, ['sku'])
            ->where('type_id = "simple"')
            ->joinLeft($_productVarcharTable,
                "{$_productVarcharTable}.attribute_id = '71' and {$_productVarcharTable}.entity_id = {$_productTable}.entity_id",
                array('name' => 'value'))
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                array('attribute_set_name'))
            ->joinLeft($_orderTable,
                "{$_orderTable}.product_id = {$_productTable}.entity_id and {$_orderTable}.created_at between '{$this->_from}' and '{$this->_to}'",
                array('period' => 'created_at',
                    'total_qty_ordered' => "sum(qty_ordered)",
                    'time' => "TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')",
                    'rate' => "7 * sum(qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}')"))
            ->joinLeft($_stockTable,
                "{$_orderTable}.product_id = {$_stockTable}.product_id",
                array('available_qty' => "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)",
                    'remaining_stock_weeks' => "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty) / (7 * sum({$_orderTable}.qty_ordered) / TIMESTAMPDIFF(DAY, if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at), '{$this->_to}'))"))
            ->joinLeft (array('suppliers' => new Zend_Db_Expr("(select pps_product_id as product_id, group_concat(sup_name separator ', ') as names from purchase_product_supplier
left join purchase_supplier
on purchase_product_supplier.pps_supplier_num = purchase_supplier.sup_id
group by pps_product_id)")),
                "suppliers.product_id = {$_productTable}.entity_id",
                array('supplier_name' => 'names'))
            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
            ->group("{$_productTable}.entity_id");
        $this->log("LowStockAvailability SQL:\n".$_select->__toString());
    }

    protected function _applyDateRangeFilter()
    {
        // Remember that field PERIOD is a DATE(YYYY-MM-DD) in all databases including Oracle
//        if ($this->_from !== null) {
//            $this->getSelect()->where("{$this->getResource()->getMainTable()}.created_at >= ?", $this->_from);
//        }
//        if ($this->_to !== null) {
//            $this->getSelect()->where("{$this->getResource()->getMainTable()}.created_at <= ?", $this->_to);
//        }

        return $this;
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

}