<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_Tree_Manage_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareColumns() {
        $this->addColumn('entity_id', [
            'header' => 'ID',
            'index' => 'entity_id'
        ]);

        $this->addColumn('name', [
            'header' => 'Name',
            'index' => 'name'
        ]);

        $this->addColumn('sku', [
            'header' => 'SKU',
            'index' => 'sku'
        ]);

        $this->addColumn('updated_at', [
            'header' => 'Last Update',
            'index' => 'updated_at',
            'type' => 'datetime'
        ]);

        $this->addColumn('edit', [
            'index' => 'entity_id',
            'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_generic_edit_link',
            'sortable' => false,
            'filterable' => false
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareCollection() {
        $this->setCollection(Mage::getModel('trsreports/product_tree')->getCollection());
    }

}
