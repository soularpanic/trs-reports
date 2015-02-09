<?php
abstract class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrder_Abstract
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row) {
        $val = parent::_getValue($row);
        return Mage::helper('trsreports/purchaseOrders')->decodePurchaseOrderData($val);
    }

}