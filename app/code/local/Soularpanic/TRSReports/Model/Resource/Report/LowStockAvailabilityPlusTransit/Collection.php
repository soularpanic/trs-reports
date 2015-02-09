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
        $_purchaseOrdersTable = 'purchase_orders';

        $_purchaseOrderSql = "(select
                                po_data.pps_product_id as product_id
                                , sum(po_data.pop_qty) - sum(po_data.pop_supplied_qty) as incoming_qty
                                , concat_ws(',', po_data.po_string) as encoded_pos
                                , concat_ws(', ', po_data.sup_name) as suppliers
                                from(select
                                    pps.pps_product_id
                                    , pop.pop_supplied_qty
                                    , pop.pop_qty
                                    , ps.sup_name
                                    , concat_ws('::', po.po_num, ps.sup_name, po.po_order_id, po.po_supply_date) as po_string
                                    from purchase_product_supplier as pps
                                        left join purchase_supplier as ps
                                            on pps.pps_supplier_num = ps.sup_id
                                        left join purchase_order_product as pop
                                            on pop.pop_product_id = pps.pps_product_id
                                                and pop.pop_supplied_qty < pop.pop_qty
                                        left join purchase_order as po
                                            on po.po_num = pop.pop_order_num
                                                and po.po_status in('new','waiting_for_delivery')
                                    where po.po_num is not null) as po_data
                                group by po_data.pps_product_id)";


        $_qtyOrdered = "sum(qty_invoiced)";
        $_startDate = "if('{$this->_from}' > {$_productTable}.created_at, '{$this->_from}', {$_productTable}.created_at)";
        $_endDate = "'{$this->_to}'";
        $_elapsedDays = "TIMESTAMPDIFF(DAY, {$_startDate}, {$_endDate})";
        $_weeklyRate = "(7 * {$_qtyOrdered} / {$_elapsedDays})";
        $_availableQty = "({$_stockTable}.qty - {$_stockTable}.stock_reserved_qty)";
        $_inTransitQty = "ifnull({$_purchaseOrdersTable}.incoming_qty, 0)";
        $_totalQty = "{$_inTransitQty} + {$_availableQty}";

        $this->getSelect()->from($_productTable, ['sku'])
            ->where("type_id = 'simple'")
            ->joinLeft($_orderTable,
                "{$_orderTable}.product_id = {$_productTable}.entity_id and {$_orderTable}.created_at between '{$this->_from}' and '{$this->_to}'",
                [ 'name',
                    'period' => 'created_at',
                    'total_qty_ordered' => $_qtyOrdered,
                    'time' => $_elapsedDays,
                    'rate' => $_weeklyRate ])
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                [ 'attribute_set_name' ])
            ->joinLeft($_stockTable,
                "{$_orderTable}.product_id = {$_stockTable}.product_id",
                [ 'available_qty' => $_availableQty ])
            ->joinLeft([$_purchaseOrdersTable => new Zend_Db_Expr($_purchaseOrderSql)],
                "{$_purchaseOrdersTable}.product_id = {$_orderTable}.product_id",
                [ 'encoded_pos',
                    'incoming_qty' => $_inTransitQty,
                    'remaining_stock_weeks' => "({$_totalQty}) / {$_weeklyRate}",
                    'suppliers',
                    'total_qty' => $_totalQty])
            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")')
            ->group("{$_orderTable}.product_id");
        $this->log("Low Stock Availability + in Transit SQL:\n".$this->getSelect()->__toString());
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