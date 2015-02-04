<?php
class Soularpanic_TRSReports_Model_Resource_Report_InStockValue_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'cataloginventory/stock_item';

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());

        $_stockTable = $this->getResource()->getMainTable();
        $_productSupplierTable = "purchase_product_supplier";

        $this->_selects = array(
            'qty' => array(
                'default' => 'qty',
                'total' => 'sum(qty)'
            ),
            'sku' => array(
                'default' => 'sku',
                'total' => '("---")'
            ),
            'name' => array(
                'default' => 'value',
                'total' => '("---")'
            ),
            'attribute_set_name' => array(
                  'total' => '("---")'
            ),
            'unit_cost' => array(
                'default' => 'pps_last_price',
                'total' => '(sum(pps_last_price))'
            ),
            'inventory_value' => array(
                'default' => "({$_productSupplierTable}.pps_last_price * {$_stockTable}.qty)",
                'total' => "(sum({$_productSupplierTable}.pps_last_price * {$_stockTable}.qty))",
            ),
            'supplier_name' => array(
                'default' => 'sup_name',
                'total' => '("---")'
            )
        );
    }

    protected function _initSelect()
    {
        $_stockTable = $this->getResource()->getMainTable();
        $_productTable = 'catalog_product_entity';
        $_attributeSetTable = 'eav_attribute_set';
        $_supplierTable = 'purchase_supplier';
        $_productNameTable = 'catalog_product_entity_varchar';
        $_productSupplierTable = "purchase_product_supplier";


        $this->getSelect()->from($_stockTable,
            $this->_getSelectCols(array('product_id', 'qty')))
            ->where("qty > 0")
            ->joinLeft($_productTable,
                "{$_stockTable}.product_id = {$_productTable}.entity_id",
                $this->_getSelectCols(array("sku")))
            ->where("{$_productTable}.type_id = 'simple'")
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                $this->_getSelectCols(array('attribute_set_name')))
            ->joinLeft($_productNameTable,
                "{$_productNameTable}.attribute_id = '71' and {$_productNameTable}.entity_id = $_productTable.entity_id",
                $this->_getSelectCols(array('name')))
            ->joinLeft($_productSupplierTable,
                "{$_productSupplierTable}.pps_product_id = {$_productTable}.entity_id",
                $this->_getSelectCols(array('unit_cost', 'inventory_value')))
            ->joinLeft($_supplierTable,
                "{$_supplierTable}.sup_id = {$_productSupplierTable}.pps_supplier_num",
                $this->_getSelectCols(array('supplier_name'))
            );
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }
}