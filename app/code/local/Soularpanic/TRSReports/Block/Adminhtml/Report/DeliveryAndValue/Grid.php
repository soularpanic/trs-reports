<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValue_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'purchase_order_name';
    protected $_resourceCollectionName = 'trsreports/report_DeliveryAndValue_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(true);
        $this->_defaultSort = 'purchase_order_name';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('purchase_order_name', [
            'header' => 'ID',
            'index' => 'purchase_order_name',
            'filter_index' => 'purchase_order_name',
            'sortable' => true
        ]);

        $this->addColumn('purchase_order_status', [
            'header' => 'Status',
            'index' => 'purchase_order_status',
            'filter_index' => 'purchase_order_status',
            'type' => 'options',
            'options' => Mage::getModel('Purchase/Order')->getStatuses()
        ]);

        $this->addColumn('supplier_name', [
            'header' => 'Supplier',
            'index' => 'supplier_name'
        ]);
//
//        $this->addColumn('date', [
//            'header' => 'Delivery Date',
//            'index' => 'supplied_dates',
//            'sortable' => false
//        ]);

        $this->addColumn('total_supplied_qty', [
            'header' => 'QTY Delivered',
            'index' => 'total_supplied_qty',
            'type' => 'number'
        ]);

        $this->addColumn('total_ordered_qty', [
            'header' => 'QTY Ordered',
            'index' => 'total_ordered_qty',
            'type' => 'number'
        ]);

        $currencyRenderer = "adminhtml/report_grid_column_renderer_currency";

        $this->addColumn('total_delivery_value', [
            'header' => 'Value Delivered',
            'index' => 'total_delivery_value',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('remaining_value', [
            'header' => 'Remaining Delivery Value',
            'index' => 'remaining_value',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('total_paid_value', [
            'header' => 'Total Paid',
            'index' => 'total_paid_value',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('remaining_balance_value', [
            'header' => 'Remaining Balance',
            'index' => 'remaining_balance_value',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD' ]);

        $this->addColumn('total_purchase_order_value', [
            'header' => 'Total Value',
            'index' => 'total_purchase_order_value',
            'renderer' => $currencyRenderer,
            'currency_code' => 'USD'
        ]);

        $this->addColumn('delivery_details', [
            'header' => 'Delivery Details',
            'index' => 'purchase_order_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_DeliveryAndValue_MoreDetails',
            'link_url' => '*/*/deliveryandvaluedeliverydetailajax',
            'link_text' => 'Toggle Deliveries',
            'prefix' => 'deliveries',
            'sortable' => false
        ]);

        $this->addColumn('payment_details', [
            'header' => 'Payment Details',
            'index' => 'purchase_order_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_DeliveryAndValue_MoreDetails',
            'link_url' => '*/*/deliveryandvaluepaymentdetailajax',
            'link_text' => 'Toggle Payments',
            'prefix' => 'payments',
            'sortable' => false
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareMassAction() {
        return $this;
    }
}