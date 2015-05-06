<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrder_IncomingQty
    extends Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrder_Abstract {

    protected function _getValue(Varien_Object $row) {
        $data = parent::_getValue($row);
        if ($data === null) {
            return '-';
        }

        $html = "";
        foreach ($data as $purchaseOrder) {
            if (!$html) {
                $html .= "<ul>";
            }

            $incoming = $purchaseOrder->getPoCountTotal() - $purchaseOrder->getPoCountArrived();
            $title = "{$purchaseOrder->getPoCode()} &mdash; {$purchaseOrder->getPoCountArrived()} / {$purchaseOrder->getPoCountTotal()} arrived";
            $url = Mage::helper('adminhtml')->getUrl("Purchase/Orders/Edit", ['po_num' => $purchaseOrder->getPoId()]);
            $html .= "<li><a title='$title' href='$url' target='_blank'>$incoming</a><br/>{$purchaseOrder->getPoExpectedArrival()}</li>";
        }

        $html .= $html ? "</ul>" : '0';

        return $html;
    }

}