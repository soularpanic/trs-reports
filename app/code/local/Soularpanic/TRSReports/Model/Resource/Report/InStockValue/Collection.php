<?php
class Soularpanic_TRSReports_Model_Resource_Report_InStockValue_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    const TOP_LEVEL_TABLE_ALIAS = "inStock";
    protected $_aggregationTable = 'cataloginventory/stock_item';

    public function __construct()
    {
        parent::__construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());
//        parent::__construct();
        $_stockTable = $this->getResource()->getMainTable();
        $_productSupplierTable = "purchase_product_supplier";

        $this->_selects = [
            'qty' => [
//                'default' => 'qty',
                'total' => 'sum(qty)' ],
            'sku' => [
                'default' => 'sku',
                'total' => '("--TOTAL--")' ],
            'name' => [
                'default' => 'value',
                'total' => '("--TOTAL--")' ],
            'attribute_set_name' => [
                'total' => '("---")' ],
            'unit_price' => [
//                'default' => 'unit_price',
                'total' => '("---")' ],
//            'inventory_value' => [
//                'default' => "({$_productSupplierTable}.pps_last_price * {$_stockTable}.qty)",
//                'total' => "(sum({$_productSupplierTable}.pps_last_price * {$_stockTable}.qty))" ],
            'total' => [
                'default' => "(qty * unit_price)",
                'total' => '(sum(qty * unit_price))'
            ],
            'suppliers' => [
//                'default' => 'sup_name',
                'total' => '("---")'
            ]
        ];
    }

    protected function _initSelect() {
        $productNameAttr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');
        $_productNameTable = $productNameAttr->getBackendTable();

        $_helper = Mage::helper('trsreports/collection');
        $inventorySelect = $_helper->getProductInventory();
        $_inventoryAlias = 'inventory';
        $_attributeNameAlias = 'attributeName';
        $_productAlias = self::TOP_LEVEL_TABLE_ALIAS;
        $_select = $this->getSelect(); // $_helper->_getNewSelect();
        $_select
            ->from([ $_inventoryAlias => $inventorySelect ],
                $this->_getSelectCols([
                    'product_id',
                    'qty',
                    'suppliers',
                    'unit_price',
                    'total' ]))
            ->joinLeft([ $_productAlias => $this->getTable('catalog/product') ],
                "$_productAlias.entity_id = $_inventoryAlias.product_id",
                $this->_getSelectCols([ 'sku' ]))
            ->joinLeft($_productNameTable,
                "{$_productNameTable}.attribute_id = '{$productNameAttr->getId()}' and {$_productNameTable}.entity_id = {$_inventoryAlias}.product_id",
                $this->_getSelectCols([ 'name' /* => "{$_productNameTable}.value" */ ]))
            ->joinLeft([ $_attributeNameAlias => $this->getTable('eav/attribute_set') ],
                "$_attributeNameAlias.attribute_set_id = $_productAlias.attribute_set_id",
                $this->_getSelectCols([ 'attribute_set_name' ]))
            ->where("unit_price > 0")
            ->where("qty > 0");

        $this->log("\n\nNEW SELECT:\n".$_select->__toString());

        $_stockTable = $this->getResource()->getMainTable();
        $_productTable = $this->getProductTable(); //'catalog_product_entity';
        $_attributeSetTable = 'eav_attribute_set';
//        $_supplierTable = 'purchase_supplier';
        $_supplierTable = $this->getTable('Purchase/Supplier');
        $_productNameTable = 'catalog_product_entity_varchar';
//        $_productSupplierTable = "purchase_product_supplier";
        $_productSupplierTable = $this->getTable('Purchase/ProductSupplier');

//        $this->getSelect()
//            ->from($_stockTable,
//                $this->_getSelectCols(array('product_id', 'qty')))
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
//                $this->_getSelectCols(array('supplier_name')))
//            ->where("qty > 0")
//            ->where("pps_last_price > 0");

        $this->log("InStockValue SQL:\n".$this->getSelect()->__toString());
    }

    protected function _applyCustomFilter() {
        $customFilterData = $this->getCustomFilterData();
        $customFilterData->setProductTable(self::TOP_LEVEL_TABLE_ALIAS);

        return parent::_applyCustomFilter();
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }
}