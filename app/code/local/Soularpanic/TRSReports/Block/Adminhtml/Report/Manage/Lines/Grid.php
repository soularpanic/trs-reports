<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Lines_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareColumns() {
        $this->addColumn('name', [
            'header' => 'Product Line Name',
            'index' => 'name',
            'filter_index' => 'name',
            'sortable' => true
        ]);

        $this->addColumn('line_sku', [
            'header' => 'Product Line SKU',
            'index' => 'line_sku',
            'filter_index'  => 'line_sku'
        ]);

        $this->addColumn('product_line_members', [
            'header' => 'Product Line Members',
            'index' => 'product_line_members',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_ProductLine_membership_list'
        ]);

        $this->addColumn('entity_id', [
            'header' => 'Edit',
            'index' => 'entity_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_ProductLine_EditLink'
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareMassAction() {
        parent::_prepareMassaction();
    }

    protected function _getCollectionClass() {
        return 'trsreports/product_line_grid_collection';
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

}