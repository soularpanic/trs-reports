<?php
class Soularpanic_TRSReports_Model_Resource_Report_InTransitValue_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_massactionIdField = 'sku';
    protected $_aggregationTable = 'Purchase/OrderProduct';

    const PRODUCT_TABLE_ALIAS = "products";
    const PRODUCT_NAME_ALIAS = 'productNames';

    public function __construct() {
        parent::__construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());

        $this->_selects = [
            'qty_incoming' => [
                'default' => 'if(pop_supplied_qty < pop_qty, pop_qty - pop_supplied_qty, 0)',
                'total' => 'sum(if(pop_supplied_qty < pop_qty, pop_qty - pop_supplied_qty, 0))'
            ],
            'sku' => [
                'default' => 'sku',
                'total' => '("--TOTAL--")'
            ],
            'product_name' => [
                'default' => self::PRODUCT_NAME_ALIAS.".value",
                'total' => '("--TOTAL--")'
            ],
            'attribute_set_name' => [
                'total' => '("---")'
            ],
            'unit_cost' => [
                'default' => 'pop_price',
                'total' => '("---")'
            ],
            'in_transit_value' => [
                'default' => "(pop_price * if(pop_supplied_qty < pop_qty, pop_qty - pop_supplied_qty, 0))",
                'total' => "(sum(pop_price * if(pop_supplied_qty < pop_qty, pop_qty - pop_supplied_qty, 0)))",
            ],
            'supplier_name' => [
                'default' => 'sup_name',
                'total' => '("---")'
            ],
            'purchase_order_id' => [
                'default' => 'po_order_id',
                'total' => '("---")'
            ]
        ];
    }

    protected function _initSelect() {
        $_helper = Mage::helper('trsreports/collection');
        $_select = $this->getSelect();
        $_purchaseOrdersAlias = 'purchaseOrders';
        $_productAlias = self::PRODUCT_TABLE_ALIAS;
        $_productNameAlias = self::PRODUCT_NAME_ALIAS;
        $_attributeNameAlias = 'attributeSets';
        $productNameAttr = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'name');
        $_purchaseOrdersSelect = $_helper->getPurchaseOrdersSelect();
        $_select
            ->from([ $_purchaseOrdersAlias => $_purchaseOrdersSelect ],
                $this->_getSelectCols([
                    'product_id',
                    'pop_order_num',
                    'pop_supplied_qty',
                    'pop_qty',
                    'qty_incoming',
                    'unit_cost',
                    'in_transit_value',
                    'purchase_order_id',
                    'po_supply_date',
                    'supplier_name' ]))
            ->joinLeft([ $_productAlias => $this->getTable('catalog/product') ],
                "$_productAlias.entity_id = $_purchaseOrdersAlias.product_id",
                $this->_getSelectCols([ 'sku' ]))
            ->joinLeft([ $_productNameAlias => $productNameAttr->getBackendTable() ],
                "{$_productNameAlias}.attribute_id = '{$productNameAttr->getId()}' and {$_productNameAlias}.entity_id = {$_purchaseOrdersAlias}.product_id",
                $this->_getSelectCols([ 'product_name' ]))
            ->joinLeft([ $_attributeNameAlias => $this->getTable('eav/attribute_set') ],
                "$_attributeNameAlias.attribute_set_id = $_productAlias.attribute_set_id",
                $this->_getSelectCols([ 'attribute_set_name' ]));

        $this->log("\n\n$_purchaseOrdersAlias select: \n".$_select->__toString());
    }

    protected function _applyCustomFilter() {
        $customFilterData = $this->getCustomFilterData();
        $customFilterData->setProductTable(self::PRODUCT_TABLE_ALIAS);

        return parent::_applyCustomFilter();
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter() {
        return $this;
    }
}