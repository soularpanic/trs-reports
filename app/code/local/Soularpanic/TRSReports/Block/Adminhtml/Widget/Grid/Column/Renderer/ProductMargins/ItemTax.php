<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_ProductMargins_ItemTax
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row) {
        $data = parent::_getValue($row);
        $helper = Mage::helper('trsreports/BundleItems');
        $price = $helper->getItemTax($data);
        return Mage::helper('core')->currency($price, true, false);
    }

    public function renderCss() {
        return 'a-right';
    }
}