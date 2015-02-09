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

        $_supplierSql = "(select pps_product_id as product_id, group_concat(sup_name separator ', ') as names from purchase_product_supplier
                            left join purchase_supplier
                            on purchase_product_supplier.pps_supplier_num = purchase_supplier.sup_id
                            group by pps_product_id)";

        $_qtyOrdered = "sum(qty_invoiced)";
        $_startDate = "if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at)";
        $_endDate = "'{$this->_to}'";
        $_elapsedDays = "TIMESTAMPDIFF(DAY, {$_startDate}, {$_endDate})";
        $_weeklyRate = "(7 * {$_qtyOrdered} / {$_elapsedDays})";
        $_availableQty = "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)";



        $_select = $this->getSelect();
        $_select->from($_productTable, ['sku'])
            ->where('type_id = "simple"')
            ->joinLeft($_productVarcharTable,
                "{$_productVarcharTable}.attribute_id = '71' and {$_productVarcharTable}.entity_id = {$_productTable}.entity_id",
                ['name' => 'value'])
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                ['attribute_set_name'])
            ->joinLeft($_orderTable,
                "{$_orderTable}.product_id = {$_productTable}.entity_id and {$_orderTable}.created_at between '{$this->_from}' and '{$this->_to}'",
                [ 'period' => 'created_at',
                    'total_qty_ordered' => $_qtyOrdered,
                    'time' => $_elapsedDays,
                    'rate' => $_weeklyRate ])
            ->joinLeft($_stockTable,
                "{$_orderTable}.product_id = {$_stockTable}.product_id",
                [ 'available_qty' => "{$_availableQty}",
                    'remaining_stock_weeks' => "{$_availableQty} / {$_weeklyRate}" ])
            ->joinLeft (['suppliers' => new Zend_Db_Expr($_supplierSql)],
                "suppliers.product_id = {$_productTable}.entity_id",
                [ 'supplier_name' => 'names' ])
            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
            ->group("{$_productTable}.entity_id");
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