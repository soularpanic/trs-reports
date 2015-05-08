<?php
class Soularpanic_TRSReports_Model_Resource_Report_InTransitValue_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    //protected $_aggregationTable = 'cataloginventory/stock_item';
    protected $_aggregationTable = 'Purchase/OrderProduct';

    public function __construct() {
        parent::__construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());

        $this->_selects = array(
            'qty' => array(
                'default' => '(pop_qty - pop_supplied_qty)',
                'total' => 'sum(pop_qty - pop_supplied_qty)'
            ),
            'sku' => array(
                'default' => 'sku',
                'total' => '("---")'
            ),
            'name' => array(
                'default' => 'pop_product_name',
                'total' => '("---")'
            ),
            'attribute_set_name' => array(
                'total' => '("---")'
            ),
            'unit_cost' => array(
                'default' => 'pop_price_ht',
                'total' => '("---")'
            ),
            'inventory_value' => array(
                'default' => "(pop_price_ht * (pop_qty - pop_supplied_qty))",
                'total' => "(sum(pop_price_ht * (pop_qty - pop_supplied_qty)))",
            ),
            'supplier_name' => array(
                'default' => 'sup_name',
                'total' => '("---")'
            )
        );
    }

    protected function _initSelect() {
//        $_productTable = $this->getProductTable(); //'catalog_product_entity';
//        $_attributeSetTable = 'eav_attribute_set';
//        $_purchaseOrderTable = 'purchase_order';
//        $_purchaseOrderProductTable = 'purchase_order_product';
//        $_supplierTable = 'purchase_supplier';
//
//        $this->getSelect()->from($_purchaseOrderTable,
//            $this->_getSelectCols(array('po_order_id')))
//            ->where('po_status not in ("complete")')
//            ->joinLeft($_purchaseOrderProductTable,
//                "{$_purchaseOrderProductTable}.pop_order_num = {$_purchaseOrderTable}.po_num",
//                $this->_getSelectCols(array('name', 'qty', 'unit_cost', 'inventory_value')))
//            ->joinLeft($_supplierTable,
//                "{$_supplierTable}.sup_id = {$_purchaseOrderTable}.po_sup_num",
//                $this->_getSelectCols(array('supplier_name')))
//            ->joinLeft($_productTable,
//                "{$_purchaseOrderProductTable}.pop_product_id = {$_productTable}.entity_id",
//                $this->_getSelectCols(array("sku")))
//             ->joinLeft($_attributeSetTable,
//                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
//                $this->_getSelectCols(array('attribute_set_name')));

    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }
}