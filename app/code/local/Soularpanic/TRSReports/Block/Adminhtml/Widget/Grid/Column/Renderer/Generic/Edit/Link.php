<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_Generic_Edit_Link
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $id = $row->getId();
        $editUrl = Mage::helper('adminhtml')->getUrl('*/*/edit', ['id' => $id]);
        $html = "<a href='{$editUrl}'>Edit</a>";
        return $html;
    }

}