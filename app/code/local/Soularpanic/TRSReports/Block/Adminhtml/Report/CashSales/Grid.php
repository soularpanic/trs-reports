<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_CashSales_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'order_number';
    protected $_resourceCollectionName = 'trsreports/report_CashSales_collection';

    public function __construct() {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(false);
        $this->_defaultSort = 'created_at';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns() {
        $this->addColumn('order_number', [
            'header'    => 'Order Number',
            'index'     => 'order_number',
            'sortable'  => false,
            'filter'    => false,
        ]);

        $this->addColumn('created_at', [
            'header'    => 'Order Date',
            'index'     => 'created_at',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'datetime'
        ]);

        $this->addColumn('grand_total', [
            'header'    => 'Order Total',
            'index'     => 'grand_total',
            'sortable'  => false,
            'filter'    => false,
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addExportType('*/*/exportCashSalesCsv', 'CSV');

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        return $this;
    }


}