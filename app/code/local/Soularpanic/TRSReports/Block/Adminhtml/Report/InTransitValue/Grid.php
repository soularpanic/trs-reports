<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InTransitValue_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_InTransitValue_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_name', array(
            'header'     => 'Item Name',
            'index'     => 'product_name',
//            'filter_index' => 'catalog_product_entity_varchar.value',
            'sortable'  => true,
        ));

        $this->addColumn('sku', array(
            'header'     => 'SKU',
            'index'     => 'sku',
            'sortable'  => true
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->addFieldToFilter('attribute_set_name', [ 'nin' => [ "Closeouts", "Internal Use", "TRS-ZHacks" ] ])
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('attribute_set_name', [
            'header'        => 'Type',
            'index'         => 'attribute_set_name',
            'filter_index'  => 'attrset.attribute_set_id',
            'sortable'      => true,
            'type'          => 'options',
            'options'       => $sets
        ]);

        $suppliers = Mage::getResourceModel('Purchase/Supplier_collection');
        $supplierOptions = [ ];
        foreach ($suppliers as $supplier) {
            $supplierOptions[$supplier->getSupName()] = $supplier->getSupName();
        }
        $this->addColumn('supplier_name', array(
            'header'    => 'Supplier',
            'index'     => 'supplier_name',
            'sortable'  => true,
            'type'          => 'options',
            'options'       => $supplierOptions,
            'total' => false
        ));

        $this->addColumn('purchase_order_id', [
            'header' => 'PO Code',
            'index' => 'purchase_order_id',
            'sortable' => true,
            'total' => false,
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_PurchaseOrderId',
        ]);

        $this->addColumn('qty_incoming', array(
            'header'    => 'QTY In-Transit',
            'index'     => 'qty_incoming',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('unit_cost', array(
            'header'    => 'Unit Cost',
            'index'     => 'unit_cost',
            'sortable'  => true,
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD',
            'total' => false
        ));

        $this->addColumn('in_transit_value', array(
            'header'    => 'In-Transit Value',
            'index'     => 'in_transit_value',
            'sortable'  => true,
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ));

        return parent::_prepareColumns();
    }

}