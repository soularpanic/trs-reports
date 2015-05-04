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

//            $html .= "{$purchaseOrder->getPoId()}<br/>";
//            $html .= "{$purchaseOrder->getPoCode()}<br/>";
//            $html .= "{$purchaseOrder->getPoCountArrived()} Arrived<br/>";
//            $html .= "{$purchaseOrder->getPoCountTotal()} Total<br/>";
            $incoming = $purchaseOrder->getPoCountTotal() - $purchaseOrder->getPoCountArrived();
            $title = "{$purchaseOrder->getPoCode()} &mdash; {$purchaseOrder->getPoCountArrived()} / {$purchaseOrder->getPoCountTotal()} arrived";
            $url = Mage::helper('adminhtml')->getUrl("Purchase/Orders/Edit", ['po_num' => $purchaseOrder->getPoId()]);
            $html .= "<li><a title='$title' href='$url'>$incoming</a><br/>{$purchaseOrder->getPoExpectedArrival()}</li>";
//            $html .= "-{$purchaseOrder->getPoExpectedArrival()}-";
        }

        $html .= $html ? "</ul>" : '0';
//        if ($html) {
//            $html .= "</ul>";
//        }
//        $suppliers = explode(',', $data);
//        $html = "<ul><li>" . implode('</li><li>', $suppliers) . "</li></ul>";

        return $html;
    }

}