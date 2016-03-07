<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValueDetail_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'product_name';
    protected $_resourceCollectionName = 'trsreports/report_DeliveryAndValueDetail_collection';

    public function __construct() {
        parent::__construct();
        $this->_defaultSort = 'product_name';
    }

    protected function _prepareColumns() {
        $this->addColumn('product_name', [
            'header' => 'Product',
            'index' => 'product_name',
            'sortable' => false
        ]);

        $this->addColumn('date', [
            'header' => 'Delivery Date',
            'index' => 'supplied_date',
            'sortable' => false
        ]);

        $this->addColumn('delivered_qty', [
            'header' => 'QTY Delivered',
            'index' => 'supplied_qty',
            'type' => 'number',
            'sortable' => false
        ]);

        $this->addColumn('delivered_value', [
            'header' => 'Value Delivered',
            'index' => 'supplied_value',
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD',
            'sortable' => false
        ]);

        $this->addColumn('remaining_value', [
            'header' => 'P/O Remaining Balance',
            'index' => 'remaining_value',
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD',
            'sortable' => false
        ]);

        return parent::_prepareColumns();
    }

    protected function _addOrderStatusFilter($collection, $filterData) {
        $collection->setCustomFilterData($filterData); // doing this here so that the getCountTotals collection has a PO ID
        return parent::_addOrderStatusFilter($collection, $filterData);
    }

    protected function _prepareMassAction() {
        return $this;
    }
}