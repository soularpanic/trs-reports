<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InternationalSalesOverview_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'region';
    protected $_resourceCollectionName = 'trsreports/report_InternationalSalesOverview_collection';

    public function __construct() {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
        $this->_defaultSort = 'region';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns() {
        $this->addColumn('region', [
            'header'    => 'Country',
            'index'     => 'region'
        ]);

        $this->addColumn('sold_value', [
            'header'        => 'Sales',
            'index'         => 'sold_value',
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('cash_value', [
            'header'        => 'Cash Sales',
            'index'         => 'cash_value',
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('credit_value', [
            'header'        => 'Credits',
            'index'         => 'credit_value',
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('refund_value', [
            'header'        => 'Refunds',
            'index'         => 'refund_value',
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('total_value', [
            'header'        => 'Total',
            'index'         => 'total_value',
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('order_count', [
            'header'    => 'Order Count',
            'index'     => 'order_count',
            'type'      => 'number'
        ]);

        $this->addColumn('order_numbers', [
            'header'    => 'Order Numbers',
            'index'     => 'order_numbers',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_OrderNumberLinksList'
        ]);

        $this->addExportType('*/*/exportInternationalSalesOverviewCsv', 'CSV');

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }


    protected function _prepareMassaction() {
        return $this;
    }
}