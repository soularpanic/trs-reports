<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailabilityPlusTransit_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailability_Grid {
//    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

//    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_LowStockAvailabilityPlusTransit_collection';

//    public function __construct()
//    {
//        parent::__construct();
//        $this->setCountTotals(false);
//        $this->setCountSubTotals(false);
//        $this->setFilterVisibility(true);
//        $this->_defaultSort = 'remaining_stock_weeks';
//        $this->_defaultDir = 'asc';
//    }

    protected function _prepareColumns() {


//        $this->removeColumn("total_qty_stock");
        $this->addColumnAfter('total_qty_stock_and_transit', [
            'header'    => 'QTY Available + Incoming',
            'index'     => 'total_qty_stock_and_transit',
            'sortable'  => true,
            'type'      => 'number'
        ], 'purchase_orders');
        parent::_prepareColumns();
//        $this->addColumn('rate', [
//            'header'            => 'Weekly Average',
//            'index'             => 'rate',
//            'filter_index'      => "(7 * total_qty_ordered / time_in_days)",
//            'type'              => 'number',
//            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
//            'decimal_places'    => 3,
//            'sortable'          => true
//        ]);
//
//        $this->addColumn('name', [
//            'header'     => 'Item Name',
//            'index'     => 'name',
//            'filter_index' => 'name',
//            'sortable'  => true
//        ]);
//
//        $this->addColumn('derived_sku', [
//            'header'        => 'SKU',
//            'index'         => 'customer_orders.derived_sku',
//            'filter_index'  => 'customer_orders.derived_sku',
//            'sortable'      => true,
//            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_pieces_sku',
//        ]);
//
//        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
//            ->addFieldToFilter('attribute_set_name', [ 'nin' => [ "Closeouts", "Internal Use", "TRS-ZHacks" ] ])
//            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
//            ->load()
//            ->toOptionHash();
//        $this->addColumn('attribute_set_name', [
//            'header'    => 'Type',
//            'index'     => 'attribute_set_name',
//            'filter_index' => 'eav_attribute_set.attribute_set_id',
//            'sortable'  => true,
//            'type'      => 'options',
//            'options'   => $sets
//        ]);
//
//        $suppliers = Mage::getResourceModel('Purchase/Supplier_collection');
//        $supplierOptions = [];
//        foreach ($suppliers as $supplier) {
//            $supplierOptions[$supplier->getSupName()] = $supplier->getSupName();
//        }
//        $this->addColumn('suppliers', [
//            'header'        => 'Supplier',
//            'index'         => 'suppliers',
//            'filter_index'  => 'suppliers',
//            'sortable'      => true,
//            'type'          => 'options',
//            'options'       => $supplierOptions
//        ]);



        //*******************************************************************************



        /*
        $this->addColumn('available_qty', [
            'header'    => 'QTY Available',
            'index'     => 'available_qty',
            'sortable'  => true,
            'type'      => 'number'
        ]);

        $this->addColumn('total_qty_ordered', [
            'header'    => 'QTY Incoming',
            'index'     => 'incoming_qty',
            'sortable'  => true,
            'type'      => 'number'
        ]);

        $this->addColumn('total_qty', [
            'header'    => 'Total QTY',
            'index'     => 'total_qty',
            'sortable'  => true,
            'type'      => 'number'
        ]);

        $this->addColumn('remaining_stock_weeks', [
            'header'    => 'Est. Weeks Left',
            'index'     => 'remaining_stock_weeks',
            'sortable'  => true,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'column_css_class'  => 'stockCell',
            'decimal_places'    => 1
        ]);

        $this->addColumn('expected_delivery_date', [
            'header'    => 'Expected Delivery Date',
            'index'     => 'encoded_pos',
            'sortable'  => true,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_ArrivalDate'
        ]);
        */
        return $this;
//        return parent::_prepareColumns();
    }

//    public function getRowClass($row) {
//        $remainingWeeks = $row->getData('remaining_stock_weeks');
//        if ($remainingWeeks) {
//            if ($remainingWeeks <= 4) {
//                return 'critical';
//            }
//            elseif ($remainingWeeks <= 12) {
//                return 'warning';
//            }
//            else {
//                return 'good';
//            }
//        }
//    }
}