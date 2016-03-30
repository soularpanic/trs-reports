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

        $this->addColumn('customer_name', [
            'header' => 'Customer Name',
            'index' => 'customer_name'
        ]);

        $this->addColumn('created_at', [
            'header'    => 'Order Date',
            'index'     => 'created_at',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'datetime'
        ]);

        $currencyRenderer = 'adminhtml/report_grid_column_renderer_currency';

        $this->addColumn('subtotal', [
            'header' => 'Product $',
            'index' => 'subtotal',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('discount_amount', [
            'header' => 'Discount',
            'index' => 'discount_amount',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('customer_credit_amount', [
            'header' => 'Internal Credit',
            'index' => 'customer_credit_amount',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('tax_amount', [
            'header' => 'Tax',
            'index' => 'tax_amount',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('grand_total', [
            'header'    => 'Order Total',
            'index'     => 'grand_total',
            'sortable'  => false,
            'filter'    => false,
            'renderer'      => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addExportType('*/*/exportCashSalesCsv', 'CSV');

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        return $this;
    }


}