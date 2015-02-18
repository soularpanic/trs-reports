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
        $_productTable = $this->getProductTable(); //'catalog_product_entity';
        $_attributeSetTable = 'eav_attribute_set';
        $_purchaseOrderTable = 'purchase_order';
        $_purchaseOrderProductTable = 'purchase_order_product';
        $_supplierTable = 'purchase_supplier';

        $this->getSelect()->from($_purchaseOrderTable,
            $this->_getSelectCols(array('po_order_id')))
            ->where('po_status not in ("complete")')
            ->joinLeft($_purchaseOrderProductTable,
                "{$_purchaseOrderProductTable}.pop_order_num = {$_purchaseOrderTable}.po_num",
                $this->_getSelectCols(array('name', 'qty', 'unit_cost', 'inventory_value')))
            ->joinLeft($_supplierTable,
                "{$_supplierTable}.sup_id = {$_purchaseOrderTable}.po_sup_num",
                $this->_getSelectCols(array('supplier_name')))
            ->joinLeft($_productTable,
                "{$_purchaseOrderProductTable}.pop_product_id = {$_productTable}.entity_id",
                $this->_getSelectCols(array("sku")))
            //->where("{$_productTable}.type_id = 'simple'")
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                $this->_getSelectCols(array('attribute_set_name')));

//        $_stockTable = $this->getResource()->getMainTable();
//        $_prouctTable = $this->getProductTable(); //'catalog_product_entity';
//        $_attributeSetTable = 'eav_attribute_set';
//        $_supplierTable = 'purchase_supplier';
//        $_productNameTable = 'catalog_product_entity_varchar';
//        $_productSupplierTable = "purchase_product_supplier";
//        $_purchaseOrderProductTable = 'purchase_order_product';
//        $_purchaseOrderTable = 'purchase_order';
//
//        $this->getSelect()->from($_stockTable,
//            $this->_getSelectCols(array('product_id')))
//            ->joinLeft($_purchaseOrderProductTable,
//            "{$_purchaseOrderProduct}.pop_product_id = {$_stockTable}.product_id",
//            array())
//            ->joinLeft($_purchaseOrderTable,
//            "{$_puchaseOrderTable}.po_num = {$_purchaseOrderProduct}.pop_order_num")
//            ->joinLeft($_productTable,
//                "{$_stockTable}.product_id = {$_productTable}.entity_id",
//                $this->_getSelectCols(array("sku")))
//            ->where("{$_productTable}.type_id = 'simple'")
//            ->joinLeft($_attributeSetTable,
//                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
//                $this->_getSelectCols(array('attribute_set_name')))
//            ->joinLeft($_productNameTable,
//                "{$_productNameTable}.attribute_id = '71' and {$_productNameTable}.entity_id = $_productTable.entity_id",
//                $this->_getSelectCols(array('name')))
//            ->joinLeft($_productSupplierTable,
//                "{$_productSupplierTable}.pps_product_id = {$_productTable}.entity_id",
//                $this->_getSelectCols(array('unit_cost', 'inventory_value')))
//            ->joinLeft($_supplierTable,
//                "{$_supplierTable}.sup_id = {$_productSupplierTable}.pps_supplier_num",
//                $this->_getSelectCols(array('supplier_name'))
//            );

    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }
}