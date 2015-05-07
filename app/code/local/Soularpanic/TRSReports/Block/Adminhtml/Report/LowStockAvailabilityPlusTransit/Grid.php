<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailabilityPlusTransit_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailability_Grid {

    protected $_resourceCollectionName = 'trsreports/report_LowStockAvailabilityPlusTransit_collection';

    protected function _prepareColumns() {

        $this->addColumnAfter('total_qty_stock_and_transit', [
            'header'    => 'QTY Available + Incoming',
            'index'     => 'total_qty_stock_and_transit',
            'sortable'  => true,
            'type'      => 'number'
        ], 'purchase_orders');

        return parent::_prepareColumns();
    }
}