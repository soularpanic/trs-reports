<?php
//class Mage_Adminhtml_Block_Catalog_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_ProductSelector_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
        $store = $this->_getStore();
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        if ($store->getId()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'custom_name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
//
//        $collection->getSelect()
//                $_productTable = $productTable ?: $this->getProductTable();
        $exclusionTable = Mage::getSingleton('core/resource')->getTableName('trsreports/excludedproduct');
        $mainAlias = 'e';
        $reportCode = $this->getReportCode();
        $collection->getSelect()
            ->where("{$mainAlias}.entity_id not in (
                SELECT product_id
                FROM $exclusionTable
                WHERE report_id = '$reportCode')");

        Mage::log("Product Selector Grid SQL:\n".$collection->getSelect()->__toString(), null, 'trs_reports.log');

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column) {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }


    protected function _prepareColumns() {
//        $this->addColumn('select',
//            array(
//                'header' => Mage::helper('catalog')->__('Select'),
//                'type' => 'massaction',
//                'index' => 'id',
//                'filter_index' => 'id'
//            ));

        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
            ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
            ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
                ));
        }

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
            ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->addFieldToFilter('attribute_set_name', array('nin' => array("Closeouts", "Internal Use", "TRS-ZHacks")))
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets
            ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
            ));
//
//        $this->addColumn('action',
//            array(
//                'header'    => Mage::helper('catalog')->__('Select'),
//                'width'     => '50px',
//                'type'      => 'action',
//                'getter'     => 'getId',
//                'actions'   => array(
//                    array(
//                        'caption' => Mage::helper('catalog')->__('Select'),
//                        'url'     => array(
//                            'base'=>'*/*/edit',
//                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
//                        ),
//                        'field'   => 'id'
//                    )
//                ),
//                'filter'    => false,
//                'sortable'  => false,
//                'index'     => 'stores',
//            ));

        return parent::_prepareColumns();
    }

//    protected function _prepareMassaction()
//    {
//        return $this;
//    }
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
        if ($this->getEnableMassaction()) {
//            $this->setMassactionIdField('entity_id');
//            $this->getMassactionBlock()->setFormFieldName('product');
//
//            $this->getMassactionBlock()->addItem('delete', array(
//                'label'=> Mage::helper('catalog')->__('Delete'),
//                'url'  => $this->getUrl('*/*/massDelete'),
//                'confirm' => Mage::helper('catalog')->__('Are you sure?')
//            ));

            $this->setMassactionIdField('entity_id');
            $this->getMassActionBlock()->setFormFieldName('product_id');
            $reportCode = $this->getReportCode();
            $this->getMassactionBlock()->addItem(
                'exclude',
                [ 'label' => $this->__('Exclude From Report'),
                    'url' => $this->getUrl('*/*/exclude', [ 'report_code' => $reportCode ])
                ]);

//            $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
//
//            array_unshift($statuses, array('label'=>'', 'value'=>''));
//            $this->getMassactionBlock()->addItem('status', array(
//                'label'=> Mage::helper('catalog')->__('Change status'),
//                'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//                'additional' => array(
//                    'visibility' => array(
//                        'name' => 'status',
//                        'type' => 'select',
//                        'class' => 'required-entry',
//                        'label' => Mage::helper('catalog')->__('Status'),
//                        'values' => $statuses
//                    )
//                )
//            ));
//
//            if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
//                $this->getMassactionBlock()->addItem('attributes', array(
//                    'label' => Mage::helper('catalog')->__('Update Attributes'),
//                    'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
//                ));
//            }

            Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));


        }
        return $this;
    }

    public function getGridUrl()
    {
        $action = $this->getEnableMassaction() ? 'productsgridwithmassaction' : 'productsgrid';
        return $this->getUrl("*/*/{$action}", array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return "javascript:dataStore.refresh({productId: '{$row->getId()}'});";
    }
}
