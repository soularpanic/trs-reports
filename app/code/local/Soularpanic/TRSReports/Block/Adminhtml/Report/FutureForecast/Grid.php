<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_FutureForecast_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_FutureForecast_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
        $this->_defaultSort = 'remaining_stock_weeks';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'         => 'Item Name',
            'index'         => 'name',
            'filter_index'  => 'name',
            'sortable'      => true
        ));

        $this->addColumn('sku', array(
            'header'         => 'SKU',
            'index'         => 'sku',
            'filter_index'  => 'sales_flat_order_item.sku',
            'sortable'      => true
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->addFieldToFilter('attribute_set_name', array('nin' => array("Closeouts", "Internal Use", "TRS-ZHacks")))
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('attribute_set_name', array(
            'header'    => 'Type',
            'index'     => 'attribute_set_name',
            'filter_index' => 'eav_attribute_set.attribute_set_id',
            'sortable'  => true,
            'type'      => 'options',
            'options'   => $sets
        ));

        $suppliers = Mage::getResourceModel('Purchase/Supplier_collection');
        $supplierOptions = array();
        foreach ($suppliers as $supplier) {
            $supplierOptions[$supplier->getSupId()] = $supplier->getSupName();
        }
        $this->addColumn('supplier_name', array(
            'header'        => 'Supplier',
            'index'         => 'supplier_name',
            'filter_index'  => 'sup_id',
            'sortable'      => true,
            'type' => 'options',
            'options' => $supplierOptions
        ));

        $this->addColumn('available_qty', array(
            'header'    => 'QTY Available',
            'index'     => 'available_qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('qty_incoming', array(
            'header'    => 'QTY Incoming',
            'index'     => 'qty_incoming',
            'sortable'  => true,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrderArrivalQty'
        ));

        $this->addColumn('future_qty', array(
            'header'            => 'Future Qty',
            'index'             => 'future_qty',
            'sortable'          => false,
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'decimal_places'    => 0
        ));

        return parent::_prepareColumns();
    }
//
//    public function getRowClass($row) {
//        $remainingWeeks = $row->getData('remaining_stock_weeks');
//        if ($remainingWeeks) {
//            if ($remainingWeeks <= 4) {
//                return 'critical';
//            }
//            elseif ($remainingWeeks <= 12) {
//                return 'warning';
//            }
//            else {
//                return 'good';
//            }
//        }
//    }
}