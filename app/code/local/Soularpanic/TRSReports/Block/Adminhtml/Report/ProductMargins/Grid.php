<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_ProductMargins_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'item_id';
    protected $_resourceCollectionName = 'trsreports/report_ProductMargins_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(true);
//        $this->_defaultSort = 'purchase_order_name';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('txn_id', [
            'header' => 'Transaction ID',
            'index' => 'txn_id'
        ]);

        $this->addColumn('order_id', [
            'header' => 'Order ID',
            'index' => 'order_id'
        ]);

        $this->addColumn('txn_date', [
            'header' => 'Transaction Date',
            'index' => 'txn_date',
            'type' => 'datetime'
        ]);

        $this->addColumn('refund_date', [
            'header' => 'Refund Date',
            'index' => 'refund_date',
            'type' => 'datetime'
        ]);

        $this->addColumn('sku', [
            'header' => 'SKU',
            'index' => 'sku'
        ]);

        $this->addColumn('name', [
            'header' => 'Name',
            'index' => 'name' ]);

        $this->addColumn("qty_ordered", [
            'header' => 'QTY Ordered',
            'index' => 'qty_ordered',
            'type' => 'number'
        ]);

        $this->addColumn('qty_refunded', [
            'header' => 'QTY Refunded',
            'index' => 'qty_refunded',
            'type' => 'number' ]);

        $this->addColumn('item_price', [
            'header' => 'Unit Price',
            'index' => 'item_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_ProductMargins_ItemPrice',
            'sortable'  => false,
            'filter'    => false
        ]);

        $this->addColumn('item_discount', [
            'header' => 'Unit Discount',
            'index' => 'item_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_ProductMargins_ItemDiscount',
            'sortable'  => false,
            'filter'    => false
        ]);

        $this->addColumn('item_tax', [
            'header' => 'Unit Tax',
            'index' => 'item_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_ProductMargins_ItemTax',
            'sortable'  => false,
            'filter'    => false
        ]);

        $currencyRenderer = "adminhtml/report_grid_column_renderer_currency";

        $this->addColumn('shipping_cost', [
            'header' => 'Shipping and Handling',
            'index' => 'shipping_cost',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD',
            'sortable' => false,
            'filter' => false
        ]);


        $this->addColumn('unit_cost', [
            'header' => 'Unit Cost',
            'index' => 'unit_cost',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD',
            'sortable'  => false,
            'filter'    => false
        ]);

        $this->addExportType('*/*/exportProductMarginsCsv', 'CSV');

        return parent::_prepareColumns();
    }

    protected function _prepareMassAction() {
        return $this;
    }
}