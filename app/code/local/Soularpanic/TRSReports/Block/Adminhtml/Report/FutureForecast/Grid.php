<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_FutureForecast_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

//    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_FutureForecast_collection';

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
        $this->addColumn('derived_name', array(
            'header'         => 'Item Name',
            'index'         => 'derived_name',
            'filter_index'  => 'derived_name',
            'sortable'      => true
        ));

        $this->addColumn('derived_sku', array(
            'header'         => 'SKU',
            'index'         => 'derived_sku',
            'filter_index'  => 'derived_sku',
            'sortable'      => true,
            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_pieces_sku',
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->addFieldToFilter('attribute_set_name', [ 'nin' => [ "Closeouts", "Internal Use", "TRS-ZHacks" ] ])
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('attribute_set_name', [
            'header'        => 'Type',
            'index'         => 'attribute_set_name',
            'filter_index'  => 'attrset.attribute_set_id',
            'sortable'      => true,
            'type'          => 'options',
            'options'       => $sets
        ]);

        $suppliers = Mage::getResourceModel('Purchase/Supplier_collection');
        $supplierOptions = [ ];
        foreach ($suppliers as $supplier) {
            $supplierOptions[$supplier->getSupName()] = $supplier->getSupName();
        }
        $this->addColumn('suppliers', [
            'header'        => 'Supplier',
            'index'         => 'suppliers',
            'filter_index'  => 'suppliers',
            'sortable'      => true,
            'type'          => 'options',
            'options'       => $supplierOptions,
            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_suppliers',
        ]);

        $this->addColumn('total_qty_stock', [
            'header'    => 'QTY Available',
            'index'     => 'total_qty_stock',
            'sortable'  => true,
            'type'      => 'number'
        ]);

        $this->addColumn('purchase_orders', [
            'header'            => 'QTY Incoming',
            'index'             => 'purchase_orders',
            'sortable'          => false,
            'filter'            => false,
            'column_css_class'  => 'a-right',
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_IncomingQty',
        ]);

        $this->addColumn('future_qty', array(
            'header'            => 'Future Qty',
            'index'             => 'future_qty',
            'sortable'          => false,
            'filter'            => false,
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'decimal_places'    => 0
        ));

        $this->addColumn('qty_to_order', array(
            'header'            => 'Qty to Order',
            'index'             => 'qty_to_order',
            'sortable'          => true,
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
//            'type' => 'number',
        'filter' => false,
            'decimal_places'    => 0
        ));

        return parent::_prepareColumns();
    }
}