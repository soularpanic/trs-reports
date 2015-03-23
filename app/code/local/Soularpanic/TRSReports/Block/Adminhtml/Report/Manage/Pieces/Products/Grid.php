<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Pieces_Products_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_ProductSelector_Grid {

    public function __construct() {
        parent::__construct();
        $this->setEnableMassaction(true);
        $this->setUseAjax(false);
    }

    protected function _setCollectionOrder($column) {
        // I guess I have to do all of this here because Magento is too fucking stupid to do it in _prepareCollection
        $collection = $this->getCollection();

        $piecedProductId = $this->getRequest()->getParam('id');

        $select = $collection->getSelect();
        $select->joinLeft(['links' => $collection->getTable('trsreports/product_piece_link')],
            "links.product_id = e.entity_id and links.pieced_product_id = '$piecedProductId'",
            ['pieced_product_id' => "(ifnull(pieced_product_id, -1))"]);

        if ($column->getId() === "pieced_product_id") {
            $index = $column->getFilterIndex() ?: $column->getIndex();
            $dir = strtoupper($column->getDir());
            $select->order("$index $dir");
        }
        Mage::log("Product Selector Grid (pieces_products_grid) SQL:\n".$collection->getSelect()->__toString(), null, 'trs_reports.log');
        $collection->clear();
        parent::_setCollectionOrder($column);
    }


    protected function _prepareColumns() {
        $this->addColumn('pieced_product_id',
            ['header'=> Mage::helper('catalog')->__('Line Member'),
                'width' => '50px',
                'index' => 'pieced_product_id',
                'filter_index' => 'pieced_product_id',
                'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_pieces_membership_boolean',
                'sortable' => true
            ]);

        parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        parent::_prepareMassaction();

        $piecedProductId = $this->getRequest()->getParam('id');
        $this->getMassactionBlock()->addItem('add',
            ['label' => $this->__('Add Pieces to Multi-Piece Product'),
                'url' => $this->getUrl('*/*/addLineProducts', ['pieced_product_id' => $piecedProductId])
            ]);

        $this->getMassactionBlock()->addItem('remove',
            ['label' => $this->__('Remove Pieces from Multi-Piece Product'),
                'url' => $this->getUrl('*/*/removeLineProducts', ['pieced_product_id' => $piecedProductId])
            ]);
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/*', ['_current' => true]);
    }

}