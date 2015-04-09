<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_Tree_Manage_Tab
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('trsreports/manage/producttrees/Tab.phtml');


    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Product Tree');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__("Product Tree");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('trsreports/adminhtml_catalog_product_productSelector_grid', 'product.select.grid')
        );

        return parent::_prepareLayout();
    }


}