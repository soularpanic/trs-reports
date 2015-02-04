<?php
class Soularpanic_TRSReports_Model_Resource_Report_PurchaseOrderOverview_Collection
    extends Soularpanic_TRSReports_Model_Resource_Report_Collection_Abstract {

    protected $_aggregationTable = 'Purchase/OrderProduct';

    public function __construct()
    {
        parent::_construct();
        $this->setModel('adminhtml/report_item');
        $this->_resource = Mage::getResourceModel('sales/report')->init($this->_aggregationTable);
        $this->setConnection($this->getResource()->getReadConnection());

        $this->_selects = array(
            'ordered_qty' => array(
                'default' => 'pop_qty',
                'subtotal' => 'sum(pop_qty)'
            ),
            'delivered_qty' => array(
                'default' => 'pop_supplied_qty',
                'subtotal' => 'sum(pop_supplied_qty)'
            ),
            'outstanding_qty' => array(
                'default' => '(pop_qty - pop_supplied_qty)',
                'subtotal' => '(sum(pop_qty) - sum(pop_supplied_qty))'
            ),
            'delivered_percent' => array(
                'default' => '(pop_supplied_qty / pop_qty)',
                'subtotal' => '(sum(pop_supplied_qty) / sum(pop_qty))'
            ),
            'sku' => array(
                'default' => 'sku',
                'subtotal' => '("---")'
            ),
            'product_name' => array(
                'default' => 'pop_product_name',
                'subtotal' => '("---")'
            ),
//            'attribute_set_name' => array(
//                'total' => '("---")'
//            ),
//            'unit_cost' => array(
//                'default' => 'pop_price_ht',
//                'total' => '("---")'
//            ),
            'price' => array(
                'default' => "pop_price_ht",
                'subtotal' => "('---')"
            ),
            'supplier_name' => array(
                'default' => 'sup_name'
            )
        );
    }

    protected function _initSelect()
    {
        $_productTable = 'catalog_product_entity';
        $_attributeSetTable = 'eav_attribute_set';
        $_purchaseOrderTable = 'purchase_order';
        $_purchaseOrderProductTable = 'purchase_order_product';
        $_supplierTable = 'purchase_supplier';

        $this->getSelect()->from($_purchaseOrderTable,
            $this->_getSelectCols(array('po_order_id')))
            ->where('po_status not in ("complete")')
            ->joinLeft($_purchaseOrderProductTable,
                "{$_purchaseOrderProductTable}.pop_order_num = {$_purchaseOrderTable}.po_num",
                $this->_getSelectCols(array('product_name', 'ordered_qty', 'delivered_qty', 'outstanding_qty', 'delivered_percent', 'price')))
            ->joinLeft($_supplierTable,
                "{$_supplierTable}.sup_id = {$_purchaseOrderTable}.po_sup_num",
                $this->_getSelectCols(array('supplier_name')))
            ->joinLeft($_productTable,
                "{$_purchaseOrderProductTable}.pop_product_id = {$_productTable}.entity_id",
                $this->_getSelectCols(array("sku")))
            ->joinLeft($_attributeSetTable,
                "{$_attributeSetTable}.attribute_set_id = {$_productTable}.attribute_set_id",
                $this->_getSelectCols(array('attribute_set_name')));

        if ($this->isSubTotals()) {
            $this->getSelect()->group('po_order_id');
        }
    }

    protected function _applyStoresFilterToSelect(Zend_Db_Select $select) {
        return $this;
    }

    protected function _applyDateRangeFilter()
    {
        return $this;
    }
}