<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_Tree_Manage_Edit
    extends Mage_Adminhtml_Block_Template {

    protected function _prepareLayout() {
        $this->setChild('grid',
            $this->getLayout()->createBlock('trsreports/adminhtml_catalog_product_productSelector_grid', 'product.select.grid')
        );

        return parent::_prepareLayout();
    }


}