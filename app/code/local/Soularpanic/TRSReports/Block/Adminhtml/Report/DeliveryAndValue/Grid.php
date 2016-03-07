<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValue_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'purchase_order_name';
    protected $_resourceCollectionName = 'trsreports/report_DeliveryAndValue_collection';

    public function __construct()
    {
        parent::__construct();
        $this->_defaultSort = 'purchase_order_name';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('purchase_order_name', [
            'header' => 'ID',
            'index' => 'purchase_order_name',
            'filter_index' => 'purchase_order_name',
            'sortable' => true
        ]);

        $this->addColumn('date', [
            'header' => 'Delivery Date',
            'index' => 'supplied_dates',
            'sortable' => false
        ]);

        $this->addColumn('total_supplied_qty', [
            'header' => 'QTY Delivered',
            'index' => 'total_supplied_qty',
            'type' => 'number'
        ]);

        $this->addColumn('total_delivery_value', [
            'header' => 'Value Delivered',
            'index' => 'total_delivery_value',
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('remaining_value', [
            'header' => 'P/O Remaining Balance',
            'index' => 'remaining_value',
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('more_details', [
            'header' => 'Details',
            'index' => 'purchase_order_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_DeliveryAndValue_MoreDetails',
            'sortable' => false
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareMassAction() {
        return $this;
    }
}