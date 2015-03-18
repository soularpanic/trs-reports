<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_ProductLine_Membership_Boolean
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $isMember = $row->getData('line_id');
        return ((int)$isMember > 0) ? "Yes" : "No";
    }

    public function renderCss() {
        return 'a-center';
    }

}