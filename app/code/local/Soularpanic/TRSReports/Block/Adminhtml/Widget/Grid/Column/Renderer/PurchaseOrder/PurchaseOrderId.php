<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrder_PurchaseOrderId
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row) {
        $data = parent::_getValue($row);
        if ($data === null) {
            return '-';
        }

        $html = $data;

        $poId = $row->getPopOrderNum();
        if ($poId) {
            $url = Mage::helper('adminhtml')->getUrl("Purchase/Orders/Edit", ['po_num' => $poId]);
            $html = "<a href='$url'>$data</a>";
        }


        return $html;
    }

}