<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Exclusions_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareColumns() {
//        $this->addColumn('product_id', [
//            'header' => 'Product ID',
//            'index' => 'product_id'
//        ]);
//
//        $this->addColumn('exclusion_id', [
//            'header' => 'Exclusion ID',
//            'index' => 'entity_id'
//        ]);

        $this->addColumn('product_name', [
            'header' => 'Product Name',
            'index' => 'product_name',
            'filter_index' => 'product_name.value'
        ]);

        $this->addColumn('product_sku', [
            'header' => 'SKU',
            'index' => 'sku',
            'filter_index'  => 'product_sku.sku',
        ]);

        $this->addColumn('report_code', [
            'header' => 'Report Code',
            'index' => 'report_id'
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
        return 'trsreports/excludedproduct_grid_collection';
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

}