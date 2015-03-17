<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Lines_Products_Grid
    extends Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_ProductSelector_Grid {

    public function __construct() {
        parent::__construct();
        $this->setEnableMassaction(true);
    }

    protected function _prepareCollection() {
        parent::_prepareCollection();
        $collection = $this->getCollection();

        $lineId = $this->getRequest()->getParam('id');

        $lineMembers = Mage::getSingleton('core/resource')->getConnection('core_read')->select();
        $lineMembers->from($collection->getTable('trsreports/product_line_link'),
            ['product_id'])
            ->where("line_id = '$lineId'");

        $collection->getSelect()->columns(['is_line_member' => "e.entity_id in ($lineMembers)"]);

        Mage::log("Product Selector Grid (lines_products_grid) SQL:\n".$collection->getSelect()->__toString(), null, 'trs_reports.log');

        $collection->clear(); // reload collection to include membership attribute
    }

    protected function _prepareColumns() {

        $this->addColumn('is_line_member',
            ['header'=> Mage::helper('catalog')->__('Line Member'),
                'width' => '50px',
                #'type'  => 'number',
                #'index' => 'is_line_member',
                'renderer' => 'trsreports/adminhtml_widget_grid_column_renderer_ProductLine_membership_boolean'
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
}