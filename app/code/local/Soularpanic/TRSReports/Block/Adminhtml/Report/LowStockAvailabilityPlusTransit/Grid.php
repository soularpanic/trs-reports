<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailabilityPlusTransit_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_LowStockAvailabilityPlusTransit_collection';

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
        $this->addColumn('rate', array(
            'header'            => 'Average',
            'index'             => 'rate',
            'filter_index'      => 'rate',
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'decimal_places'    => 3,
            'sortable'          => true
        ));

        $this->addColumn('name', array(
            'header'     => 'Item Name',
            'index'     => 'name',
            'filter_index' => 'name',
            'sortable'  => true
        ));

        $this->addColumn('sku', array(
            'header'     => 'SKU',
            'index'     => 'sku',
            'filter_index' => 'catalog_product_entity.sku',
            'sortable'  => true
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
        $this->addColumn('suppliers', array(
            'header'    => 'Supplier',
            'index'     => 'suppliers',
            'filter_index' => 'suppliers.suppliers',
            'sortable'  => true,
            'type' => 'options',
            'options' => $supplierOptions
        ));

        $this->addColumn('available_qty', array(
            'header'    => 'QTY Available',
            'index'     => 'available_qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('incoming_qty', array(
            'header'    => 'QTY Incoming',
            'index'     => 'incoming_qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('total_qty', array(
            'header'    => 'Total QTY',
            'index'     => 'total_qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('remaining_stock_weeks', array(
            'header'    => 'Est. Weeks Left',
            'index'     => 'remaining_stock_weeks',
            'sortable'  => true,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'column_css_class'  => 'stockCell',
            'decimal_places'    => 1
        ));

        $this->addColumn('po_supply_date', array(
            'header'    => 'Expected Delivery Date',
            'index'     => 'encoded_pos',
            'sortable'  => true,
            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_ArrivalDate'
//            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrderArrivalDate',
        ));
//        $this->addExportType('*/*/exportSalesCsv', Mage::helper('adminhtml')->__('CSV'));
//        $this->addExportType('*/*/exportSalesExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    public function getRowClass($row) {
        $remainingWeeks = $row->getData('remaining_stock_weeks');
        if ($remainingWeeks) {
            if ($remainingWeeks <= 4) {
                return 'critical';
            }
            elseif ($remainingWeeks <= 12) {
                return 'warning';
            }
            else {
                return 'good';
            }
        }
    }
}