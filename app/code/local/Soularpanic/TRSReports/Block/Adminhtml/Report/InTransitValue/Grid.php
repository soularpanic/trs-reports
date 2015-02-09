<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InTransitValue_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_InTransitValue_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'     => 'Item Name',
            'index'     => 'name',
            'filter_index' => 'catalog_product_entity_varchar.value',
            'sortable'  => true,
        ));

        $this->addColumn('sku', array(
            'header'     => 'SKU',
            'index'     => 'sku',
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
            'sortable'  => true,
            'total' => false
        ));

        $this->addColumn('qty', array(
            'header'    => 'QTY In-Transit',
            'index'     => 'qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('unit_cost', array(
            'header'    => 'Unit Cost',
            'index'     => 'unit_cost',
            'sortable'  => true,
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ));

        $this->addColumn('inventory_value', array(
            'header'    => 'Inventory Value',
            'index'     => 'inventory_value',
            'sortable'  => true,
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ));

        return parent::_prepareColumns();
    }

}