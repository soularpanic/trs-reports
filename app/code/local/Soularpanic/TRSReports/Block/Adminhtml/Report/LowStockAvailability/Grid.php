<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_LowStockAvailability_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'derived_id';
    protected $_resourceCollectionName = 'trsreports/report_LowStockAvailability_collection';

    public function __construct() {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
        $this->_defaultSort = 'remaining_stock_weeks';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns() {
        $this->addColumn('rate', array(
            'header'            => 'Average',
            'index'             => 'rate',
            'filter_index'      => 'rate',
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'decimal_places'    => 3,
            'sortable'          => true
        ));

        $this->addColumn('name', array(
            'header'        => 'Item Name',
            'index'         => 'name',
            'filter_index'  => 'name',
            'sortable'      => true
        ));

        $this->addColumn('sku', array(
            'header'        => 'SKU',
            'sortable'      => true,
            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_ProductLine_sku',
            'filter_condition_callback' => [$this, '_filterDerivedSku']
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
            $supplierOptions[$supplier->getSupName()] = $supplier->getSupName();
        }
        $this->addColumn('supplier_names', array(
            'header'        => 'Supplier',
            'index'         => 'supplier_names',
            'filter_index'  => 'suppliers.suppliers',
            'sortable'      => true,
            'type'          => 'options',
            'options'       => $supplierOptions
        ));

        $this->addColumn('available_qty', array(
            'header'    => 'QTY Available',
            'index'     => 'available_qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('total_qty_ordered', array(
            'header'    => 'QTY Incoming',
            'index'     => 'incoming_qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('remaining_stock_weeks', array(
            'header'            => 'Est. Weeks Left',
            'index'             => 'remaining_stock_weeks',
            'sortable'          => true,
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_FlooredDecimal',
            'column_css_class'  => 'stockCell',
            'decimal_places'    => 1
        ));

        return parent::_prepareColumns();
    }


    protected function _filterDerivedSku($collection, $column) {
        $value = $column->getFilter()->getValue();

        if (!$value) {
            return $this;
        }

        $_select = $collection->getSelect();
        $_select->where("catalog_product_entity.sku like ? OR lines.line_sku like ?", "%$value%");
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