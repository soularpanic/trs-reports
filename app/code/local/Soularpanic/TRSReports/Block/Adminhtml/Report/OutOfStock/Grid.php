<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_OutOfStock_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_OutOfStock_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
        $this->_defaultSort = 'remaining_stock_weeks';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'     => 'Item Name',
            'index'     => 'name',
            'filter_index' => 'catalog_product_entity_varchar.value',
            'sortable'  => true
        ));

        $this->addColumn('sku', array(
            'header'     => 'SKU',
            'index'     => 'sku',
            'filter_index' => 'catalog_product_entity.sku',
            'sortable'  => true
        ));

        $this->addColumn('attribute_set_name', array(
            'header'    => 'Type',
            'index'     => 'attribute_set_name',
            'sortable'  => true
        ));

        $this->addColumn('supplier_name', array(
            'header'    => 'Supplier',
            'index'     => 'supplier_name',
            'filter_index' => 'purchase_supplier.sup_name',
            'sortable'  => true
        ));

        $this->addColumn('qty', array(
            'header'    => 'Stock Summary',
            'index'     => 'qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('po_supply_date', array(
            'header'    => 'Expected Delivery Date',
            'index'     => 'po_supply_date',
            'sortable'  => true,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrderArrivalDate',
        ));

        return parent::_prepareColumns();
    }
}