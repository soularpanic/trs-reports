<?php
class Soularpanic_TRSReports_Model_Resource_Report_OutOfStock_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'cataloginventory/stock_item';

    protected $_selectedColumns    = array();

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());
    }

    protected function _initSelect()
    {
        $_stockTable = $this->getResource()->getMainTable();
        $_productTable = $this->getProductTable(); //'catalog_product_entity';
        $_attributeSetTable = 'eav_attribute_set';
        $_supplierTable = 'purchase_supplier';
        $_productNameTable = 'catalog_product_entity_varchar';
        $_purchaseOrderItemsTable = 'purchase_order_product';
        $_purchaseOrderTable = 'purchase_order';
        $this->getSelect()->from($_stockTable,
            array(
                'product_id',
                'qty'
            ))
            ->where("qty <= 0")
            ->joinLeft($_productTable,
                "{$_stockTable}.product_id = {$_productTable}.entity_id",
                array("sku"))
            ->where("{$_productTable}.type_id = 'simple'")
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                array('attribute_set_name'))
            ->joinLeft($_productNameTable,
                "{$_productNameTable}.attribute_id = '71' and {$_productNameTable}.entity_id = $_productTable.entity_id",
                array('name' => 'value'))
            ->joinLeft($_purchaseOrderItemsTable,
                "{$_purchaseOrderItemsTable}.pop_product_id = {$_stockTable}.product_id",
                array())
            ->joinLeft($_purchaseOrderTable,
                "{$_purchaseOrderItemsTable}.pop_order_num = {$_purchaseOrderTable}.po_num and {$_purchaseOrderTable}.po_status in('new', 'waiting_for_delivery')",
                array(
                    "po_id" => "po_num",
                    "po_number" => "po_order_id",
                    "po_supply_date"))
            ->joinLeft($_supplierTable,
                "{$_supplierTable}.sup_id = {$_purchaseOrderTable}.po_sup_num",
                array('supplier_name' => 'sup_name'))
            ->where('attribute_set_name is not null and attribute_set_name not in("Closeouts", "Internal Use", "TRS-ZHacks")');
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }
}