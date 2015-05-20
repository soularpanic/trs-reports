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

        $this->_selects = [
            'qty' => [
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
                'total' => '("---")' ],
            'total' => [
                'default' => "(qty * unit_price)",
                'total' => '(sum(qty * unit_price))'
            ],
            'suppliers' => [
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
        $_select = $this->getSelect();
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
                $this->_getSelectCols([ 'name' ]))
            ->joinLeft([ $_attributeNameAlias => $this->getTable('eav/attribute_set') ],
                "$_attributeNameAlias.attribute_set_id = $_productAlias.attribute_set_id",
                $this->_getSelectCols([ 'attribute_set_name' ]))
            ->where("unit_price > 0")
            ->where("qty > 0");

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