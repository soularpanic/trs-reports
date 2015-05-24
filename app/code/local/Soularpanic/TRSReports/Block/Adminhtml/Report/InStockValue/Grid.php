<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InStockValue_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'sku';
    protected $_massactionIdField = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_InStockValue_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
    }

    public function getTotals() {
        return parent::getTotals();
    }

     protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'     => 'Item Name',
            'index'     => 'name',
            'filter_index' => 'catalog_product_entity_varchar.value',
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
        $this->addColumn('suppliers', [
            'header'        => 'Supplier',
            'index'         => 'suppliers',
            'filter_index'  => 'suppliers',
            'sortable'      => true,
            'type'          => 'options',
            'options'       => $supplierOptions,
//            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_suppliers',
        ]);


        $this->addColumn('available_qty', array(
            'header'    => 'QTY Available',
            'index'     => 'qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('unit_price', array(
            'header'    => 'Unit Cost',
            'index'     => 'unit_price',
            'sortable'  => true,
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ));

        $this->addColumn('total', array(
            'header'    => 'Inventory Value',
            'index'     => 'total',
            'sortable'  => true,
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ));

        return parent::_prepareColumns();
    }

}