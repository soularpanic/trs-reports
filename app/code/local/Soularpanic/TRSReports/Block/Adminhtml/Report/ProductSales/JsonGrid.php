<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_ProductSales_JsonGrid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_JsonGrid_Abstract {

    public function __construct() {
        parent::__construct();
        $this->setId("salesGrid");
        $this->setFilterVisibility(false);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('date', array(
            'header'     => 'Date',
            'index'     => 'date',
            'path' => 'actual_sold',
        ));

        $this->addColumn('actualSold', array(
            'header' => 'Actual Sold',
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

        return parent::_prepareColumns();
    }
}