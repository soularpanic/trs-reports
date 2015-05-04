<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailability_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'derived_id';
    protected $_resourceCollectionName = 'trsreports/report_LowStockAvailability_collection';

    public function __construct() {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
        $this->_defaultSort = 'estimated_remaining_weeks';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns() {
        $this->addColumn('weekly_rate', [
            'header'            => 'Weekly Average',
            'index'             => 'weekly_rate',
            'filter_index'      => "(7 * total_qty_sold / time_in_days)",
            'type'              => 'number',
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'decimal_places'    => 3,
            'sortable'          => true
        ]);

        $this->addColumn('derived_name', [
            'header'        => 'Item Name',
            'index'         => 'derived_name',
            'filter_index'  => 'derived_name',
            'sortable'      => true
        ]);

        $this->addColumn('derived_sku', [
            'header'        => 'SKU',
            'index'         => 'derived_sku',
            'sortable'      => true,
            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_pieces_sku',
        ]);

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
            'sortable'          => true,
            'column_css_class'  => 'a-right',
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_IncomingQty',
        ]);

        $this->addColumn('estimated_remaining_weeks', [
            'header'            => 'Est. Weeks Left',
            'index'             => 'estimated_remaining_weeks',
            'sortable'          => true,
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'column_css_class'  => 'stockCell',
            'decimal_places'    => 1
        ]);

        return parent::_prepareColumns();
    }

    public function getRowClass($row) {
        $remainingWeeks = $row->getData('estimated_remaining_weeks');
        if ($remainingWeeks) {
            if ($remainingWeeks <= 4) {
                return 'critical';
            }
            elseif ($remainingWeeks <= 12) {
                return 'warning';
            }
            else {
                return 'good';
            }
        }
    }
}