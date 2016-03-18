<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValuePaymentDetail_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'entity_id';
    protected $_resourceCollectionName = 'trsreports/report_DeliveryAndValuePaymentDetail_collection';

    public function __construct() {
        parent::__construct();
        $this->_defaultSort = 'created_at';
        $this->_defaultDir = 'desc';
    }

    protected function _prepareColumns() {

        $this->addColumn('created_at', [
            'header' => 'Paid Date',
            'index' => 'created_at',
            'type' => 'date',
            'sortable' => false
        ]);

        $this->addColumn('details', [
            'header' => 'Details',
            'index' => 'details',
            'sortable' => false
        ]);

        $this->addColumn('payment', [
            'header' => 'Payment',
            'index' => 'payment',
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