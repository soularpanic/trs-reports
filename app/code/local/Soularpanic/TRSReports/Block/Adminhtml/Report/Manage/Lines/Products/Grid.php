<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Lines_Products_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_ProductSelector_Grid {

    public function __construct() {
        parent::__construct();
        $this->setEnableMassaction(true);
        $this->setUseAjax(false);
    }

    protected function _setCollectionOrder($column) {
        // I guess I have to do all of this here because Magento is too fucking stupid to do it in _prepareCollection
        $collection = $this->getCollection();

        $lineId = $this->getRequest()->getParam('id');

        $select = $collection->getSelect();
        $select->joinLeft(['links' => $collection->getTable('trsreports/product_line_link')],
            "links.product_id = e.entity_id and links.line_id = '$lineId'",
            ['line_id' => "(ifnull(line_id, -1))"]);

        if ($column->getId() === "line_id") {
            $index = $column->getFilterIndex() ?: $column->getIndex();
            $dir = strtoupper($column->getDir());
            $select->order("$index $dir");
        }
        Mage::log("Product Selector Grid (lines_products_grid) SQL:\n".$collection->getSelect()->__toString(), null, 'trs_reports.log');
        $collection->clear();
        parent::_setCollectionOrder($column);
    }


    protected function _prepareColumns() {
        $this->addColumn('line_id',
            ['header'=> Mage::helper('catalog')->__('Line Member'),
                'width' => '50px',
                'index' => 'line_id',
                'filter_index' => 'line_id',
                'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_ProductLine_membership_boolean',
                'sortable' => true
            ]);

        parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        parent::_prepareMassaction();

        $lineId = $this->getRequest()->getParam('id');
        $this->getMassactionBlock()->addItem('add',
            ['label' => $this->__('Add Products to Line'),
                'url' => $this->getUrl('*/*/addLineProducts', ['line_id' => $lineId])
            ]);

        $this->getMassactionBlock()->addItem('remove',
            ['label' => $this->__('Remove Products from Line'),
                'url' => $this->getUrl('*/*/removeLineProducts', ['line_id' => $lineId])
            ]);
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/*', ['_current' => true]);
    }

}