<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrder_Suppliers
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row) {
        $data = parent::_getValue($row);
        if ($data === null) {
            return '-';
        }

        $suppliers = explode(',', $data);
        $html = "<ul><li>" . implode('</li><li>', $suppliers) . "</li></ul>";

        return $html;
    }

}