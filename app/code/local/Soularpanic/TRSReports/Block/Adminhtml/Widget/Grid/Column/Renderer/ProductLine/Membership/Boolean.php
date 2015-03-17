<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_ProductLine_Membership_Boolean
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $isMember = $row->getData('is_line_member');
        return $isMember ? "Yes" : "No";
    }

}