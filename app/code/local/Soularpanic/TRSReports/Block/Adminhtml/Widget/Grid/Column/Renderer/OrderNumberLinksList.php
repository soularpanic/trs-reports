<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_OrderNumberLinksList
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    const LIMIT = 35;

    protected function _getValue(Varien_Object $row)
    {
        $html = $this->getColumn()->getDefault();

        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $html = "<ul>";
            $orderPairs = explode(',', $data);
            $i = 0;
            foreach($orderPairs as $orderPair) {
                if ($i >= self::LIMIT) {
                    $html.= "<li>...</li>";
                    break;
                }
                list($systemId, $incrementId) = explode(":", $orderPair);
                $url = Mage::helper('adminhtml')->getUrl('*/sales_order/view', ['order_id' => $systemId]);
                $html.= "<li><a href='{$url}'>{$incrementId}</a></li>";
                $i++;
            }
            $html.= "</ul>";
        }
        return $html;
    }

}