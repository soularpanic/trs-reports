<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_SalesTax_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_resourceCollectionName = 'trsreports/report_SalesTax_collection';

    public function __construct() {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(false);
        $this->_defaultSort = 'region';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns() {
        $this->addColumn('region', [
            'header'    => 'Region',
            'index'     => 'region',
            'sortable'  => false,
            'filter'    => false,
        ]);

        $this->addColumn('subtotal', [
            'header'        => 'Orders Total',
            'index'         => 'subtotal',
            'sortable'      => false,
            'filter'        => false,
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('tax_total', [
            'header'        => 'Tax Total',
            'index'         => 'tax_total',
            'sortable'      => false,
            'filter'        => false,
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('calculated_taxed_sales', [
            'header'        => 'Taxed',
            'index'         => 'calculated_taxed_sales',
            'sortable'      => false,
            'filter'        => false,
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addColumn('calculated_tax_exempt_sales', [
            'header'        => 'Tax Exempt',
            'index'         => 'calculated_tax_exempt_sales',
            'sortable'      => false,
            'filter'        => false,
            'renderer'      => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ]);

        $this->addExportType('*/*/exportSalesTaxCsv', 'CSV');

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        return $this;
    }


}