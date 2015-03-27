<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_AttributeSetSelector_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('attributeSetGrid');
        $this->setDefaultSort('set_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->addFieldToFilter('attribute_set_name', array('nin' => array("Closeouts", "Internal Use", "TRS-ZHacks")))
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('set_name', array(
            'header'    => Mage::helper('catalog')->__('Set Name'),
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'attribute_set_name',
        ));
    }

    public function getRowUrl($row) {
        return "javascript:dataStore.refresh({attributeSetId: '{$row->getId()}'});";
    }

}
