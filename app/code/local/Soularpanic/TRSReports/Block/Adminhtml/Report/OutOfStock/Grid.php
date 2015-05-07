<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_OutOfStock_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

//    protected $_columnGroupBy = 'derived_id';
//    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_OutOfStock_collection';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(false);
        $this->setFilterVisibility(true);
        $this->_defaultSort = 'remaining_stock_weeks';
        $this->_defaultDir = 'asc';
    }

    protected function _prepareColumns() {
        $this->addColumn('derived_name', [
            'header'        => 'Item Name',
            'index'         => 'derived_name',
            'filter_index'  => 'derived_name',
            'sortable'      => true
        ]);

        $this->addColumn('derived_sku', [
            'header'        => 'SKU',
            'index'         => 'derived_sku',
            'sortable'      => true,
            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_pieces_sku',
        ]);
//        $this->addColumn('name', array(
//            'header'     => 'Item Name',
//            'index'     => 'name',
//            'filter_index' => 'catalog_product_entity_varchar.value',
//            'sortable'  => true
//        ));
//
//        $this->addColumn('sku', array(
//            'header'     => 'SKU',
//            'index'     => 'sku',
//            'filter_index' => 'catalog_product_entity.sku',
//            'sortable'  => true
//        ));

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
//        $this->addColumn('attribute_set_name', array(
//            'header'    => 'Type',
//            'index'     => 'attribute_set_name',
//            'sortable'  => true
//        ));
//
//        $this->addColumn('supplier_name', array(
//            'header'    => 'Supplier',
//            'index'     => 'supplier_name',
//            'filter_index' => 'purchase_supplier.sup_name',
//            'sortable'  => true
//        ));

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
            'renderer'      => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_suppliers',
        ]);

//        $this->addColumn('qty', array(
//            'header'    => 'Stock Summary',
//            'index'     => 'qty',
//            'sortable'  => true,
//            'type'      => 'number'
//        ));
//
//        $this->addColumn('po_supply_date', array(
//            'header'    => 'Expected Delivery Date',
//            'index'     => 'po_supply_date',
//            'sortable'  => true,
//            'renderer'  => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrderArrivalDate',
//        ));

        $this->addColumn('total_qty_stock', [
            'header'    => 'QTY Available',
            'index'     => 'total_qty_stock',
            'sortable'  => true,
            'type'      => 'number'
        ]);

        $this->addColumn('purchase_orders', [
            'header'            => 'QTY Incoming',
            'index'             => 'purchase_orders',
            'sortable'          => true,
            'column_css_class'  => 'a-right',
            'renderer'          => 'trsreports/adminhtml_widget_grid_column_renderer_PurchaseOrder_IncomingQty',
        ]);

        return parent::_prepareColumns();
    }
}