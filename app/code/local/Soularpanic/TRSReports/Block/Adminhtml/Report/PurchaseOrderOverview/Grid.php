<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_PurchaseOrderOverview_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract {

    protected $_columnGroupBy = 'sku';
    protected $_resourceCollectionName = 'trsreports/report_PurchaseOrderOverview_collection';
    protected $_lastRow = null;

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(false);
        $this->setCountSubTotals(true);
        $this->setFilterVisibility(true);
        $this->setSubtotalVisibility(true);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('supplier_name', array(
            'header'    => 'Supplier',
            'index'     => 'supplier_name',
            'sortable'  => true,
            'filter_index' => 'purchase_supplier.sup_name'
        ));

        $this->addColumn('po_order_id', array(
            'header'     => 'P/O Number',
            'index'     => 'po_order_id',
            'sortable'  => true,
        ));

        $this->addColumn('product_name', array(
            'header'     => 'Item Name',
            'index'     => 'product_name',
            'filter_index' => 'catalog_product_entity_varchar.value',
            'sortable'  => true
        ));

        $this->addColumn('sku', array(
            'header'     => 'SKU',
            'index'     => 'sku',
            'sortable'  => true
        ));

        $this->addColumn('price', array(
            'header'    => 'Unit Price',
            'index'     => 'price',
            'sortable'  => true,
            'renderer' => 'adminhtml/report_grid_column_renderer_currency',
            'currency_code' => 'USD'
        ));

        $this->addColumn('ordered_qty', array(
            'header'    => 'QTY Ordered',
            'index'     => 'ordered_qty',
            'sortable'  => true,
            'type'      => 'number'
        ));

        $this->addColumn('delivered_qty', array(
            'header'    => 'QTY Delivered',
            'index'     => 'delivered_qty',
            'sortable'  => true,
            'type'      => 'number',
        ));

        $this->addColumn('outstanding_qty', array(
            'header'    => 'QTY Outstanding',
            'index'     => 'outstanding_qty',
            'sortable'  => true,
            'type'      => 'number',
        ));

        $this->addColumn('delivered_percent', array(
            'header'    => '% Delivered',
            'index'     => 'delivered_percent',
            'sortable'  => true,
            'type'      => 'number',
            'renderer' => 'trsreports/Adminhtml_Widget_Grid_Column_Renderer_Percent'
        ));

        return parent::_prepareColumns();
    }

    public function getSubTotalItem($item)
    {
        $keyColumn = 'po_order_id';
        foreach ($this->_subtotals as $subtotalItem) {
            if ($subtotalItem->getData($keyColumn) == $item->getData($keyColumn)) {
                return $subtotalItem;
            }
        }
        return '';
    }


    public function shouldRenderSubTotal($item) {
        if (!$this->_countSubTotals || count($this->_subtotals) <= 0) {
            return false;
        }

        $itemKey = $item->getData('po_order_id') . '::' . $item->getData('product_name');
        $collectionArr = $this->getCollection()->getItems();
        for($i = 0; $i < count($collectionArr); $i++) {
            $candidate = current($collectionArr);

            if ($candidate === false) {
                return false;
            }

            $candidateKey = $candidate->getData('po_order_id') . '::' . $candidate->getData('product_name');
            if ($candidateKey === $itemKey) {
                $nextItem = next($collectionArr);
                return ($nextItem === false || $nextItem->getData('po_order_id') !== $item->getData('po_order_id'));
            }
            next($collectionArr);
        }
        return false;
    }

}