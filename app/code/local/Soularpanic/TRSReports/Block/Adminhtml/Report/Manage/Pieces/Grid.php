<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Pieces_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareColumns() {
        $this->addColumn('name', [
            'header' => 'Multi-Piece Product Name',
            'index' => 'name',
            'filter_index' => 'name',
            'sortable' => true
        ]);

        $this->addColumn('pieced_product_sku', [
            'header' => 'Multi-Piece Product SKU',
            'index' => 'pieced_product_sku',
            'filter_index'  => 'pieced_product_sku'
        ]);

        $this->addColumn('product_line_members', [
            'header' => 'Product Pieces',
            'index' => 'product_line_members',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_pieces_membership_list'
        ]);

        $this->addColumn('entity_id', [
            'header' => 'Edit',
            'index' => 'entity_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_pieces_EditLink'
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareMassAction() {
        parent::_prepareMassaction();
    }

    protected function _getCollectionClass() {
        return 'trsreports/product_piece_product_grid_collection';
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

}