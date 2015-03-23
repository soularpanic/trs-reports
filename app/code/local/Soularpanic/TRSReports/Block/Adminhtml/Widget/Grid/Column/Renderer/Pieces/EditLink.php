<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_Pieces_EditLink
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $lineId = $row->getData('entity_id');
        $editUrl = Mage::helper('adminhtml')->getUrl('*/*/edit', ['id' => $lineId]);
        $html = "<a href='{$editUrl}'>Edit</a>";
        return $html;
    }

}