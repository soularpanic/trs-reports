<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Lines_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareColumns() {
        $this->addColumn('name', [
            'header' => 'Product Line Name',
            'index' => 'name',
            'filter_index' => 'name'
        ]);

        $this->addColumn('line_sku', [
            'header' => 'Product Line SKU',
            'index' => 'line_sku',
            'filter_index'  => 'line_sku'
        ]);

        $this->addColumn('product_line_members', [
            'header' => 'Product Line Members',
            'index' => 'product_line_members'
        ]);

        return parent::_prepareColumns();
    }

    protected function _prepareMassAction() {
        parent::_prepareMassaction();
        $this->setMassactionIdField('entity_id');
        $this->getMassActionBlock()->setFormFieldName('entity_id');
        $this->getMassactionBlock()->addItem(
            'exclude',
            [ 'label' => $this->__('Un-exclude From Report'),
                'url' => $this->getUrl('*/*/unexclude')]
        );
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