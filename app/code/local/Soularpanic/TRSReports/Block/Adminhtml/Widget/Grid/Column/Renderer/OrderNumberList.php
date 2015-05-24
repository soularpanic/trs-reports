<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_OrderNumberList
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    const LIMIT = 35;

    protected function _getValue(Varien_Object $row)
    {
        $html = $this->getColumn()->getDefault();

        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $html = "";
            $orderPairs = explode(',', $data);
            $i = 0;
            foreach($orderPairs as $orderPair) {
                if ($i >= self::LIMIT) {
                    $html.= "<li>...</li>";
                    break;
                }
                list($systemId, $incrementId) = explode(":", $orderPair);
                $html.= "$incrementId\n";
                $i++;
            }
        }
        return $html;
    }

}