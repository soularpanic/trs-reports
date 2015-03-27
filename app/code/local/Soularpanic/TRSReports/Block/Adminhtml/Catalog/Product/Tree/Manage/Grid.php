<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_Tree_Manage_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareColumns() {
        $this->addColumn('entity_id', [
            'header' => 'ID',
            'index' => 'entity_id'
        ]);
        return parent::_prepareColumns();
    }

    protected function _prepareCollection() {
        $this->setCollection(Mage::getModel('catalog/product')->getCollection());
    }


}
