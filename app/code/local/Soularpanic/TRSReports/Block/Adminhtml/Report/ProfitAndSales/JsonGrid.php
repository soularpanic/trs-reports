<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_ProfitAndSales_JsonGrid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_JsonGrid_Abstract {

    public function __construct() {
        parent::__construct();
        $this->setId("salesGrid");
        $this->setFilterVisibility(false);
    }

    protected function _prepareColumns() {
        $this->addColumn('date', array(
           'header' => 'Date',
            'index' => 'date',
            'path' => 'actual_sold'
        ));

        $this->addColumn('name', array(
            'header'     => 'Name',
            'index'     => 'label',
            'path' => 'meta'
            //'meta' => true
        ));

        $this->addColumn('actualSold', array(
            'header' => 'Actual Sales',
            'index' => 'sold',
            'path' => 'actual_sold',
            'format' => 'int'
        ));

        $this->addColumn('avgSold', array(
            'header' => 'Average Sold',
            'index' => 'sold',
            'path' => 'avg_sold',
            'format' => 'decimal'
        ));

        $this->addColumn('totalRevenue', array(
            'header' => 'Total Revenue',
            'index' => 'revenue',
            'path' => 'actual_sold',
            'format' => 'currency'
        ));

        $this->addColumn('totalCost', array(
            'header' => 'Cost of Goods Sold',
            'index' => 'cost',
            'path' => 'actual_sold',
            'format' => 'currency'
        ));
//
        $this->addColumn('grossProfit', array(
            'header' => 'Gross Profit',
            'index' => 'profit',
            'path' => 'actual_sold',
            'format' => 'currency'
        ));
//
//        $this->addColumn('avgMargin', array(
//            'header' => 'Average Margin',
//            'index' => 'sold',
//            'path' => 'avg_sold'
//        ));

        return parent::_prepareColumns();
    }
}