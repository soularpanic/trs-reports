<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_DeliveryAndValue_MoreDetails
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row) {
        $data = parent::_getValue($row);
        $req = Mage::app()->getRequest();
        $base64Filter = $req->getParam('filter');
        $filterValues = Mage::helper('adminhtml')->prepareFilterString($base64Filter);
        $filterValues['purchase_order_number'] = $data;

        $newFilterStr = "";
        foreach ($filterValues as $key => $value) {
            if (strlen($newFilterStr) > 0) {
                $newFilterStr.= '&';
            }
            $newFilterStr.="$key=".rawurlencode($value);
        }

        $newFilter = base64_encode($newFilterStr);

        $url = Mage::helper('adminhtml')->getUrl('*/*/deliveryandvaluedetailajax', [ 'filter' => $newFilter ]);

        return "<a class='moreDetails' data-url='$url' data-containerId='$data'>Toggle Details</a>";
    }
}