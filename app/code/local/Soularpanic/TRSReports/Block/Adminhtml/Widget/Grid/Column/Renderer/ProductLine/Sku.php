<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_ProductLine_Sku
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $sku = $row->getData('derived_sku');
        $isProductLine = $row->getData('is_product_line');
        $html = ($isProductLine ? "<em>$sku</em>" : $sku);
        return $html;
    }

}