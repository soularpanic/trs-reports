<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InternationalSalesOverview_CsvGrid
extends Soularpanic_TRSReports_Block_Adminhtml_Report_InternationalSalesOverview_Grid {

    protected function _prepareColumns() {
        parent::_prepareColumns();

        $this->removeColumn('order_numbers');

        $this->addColumn('order_numbers', [
            'header'    => 'Order Numbers',
            'index'     => 'order_numbers',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_OrderNumberList'
        ]);
        return $this;
    }
}