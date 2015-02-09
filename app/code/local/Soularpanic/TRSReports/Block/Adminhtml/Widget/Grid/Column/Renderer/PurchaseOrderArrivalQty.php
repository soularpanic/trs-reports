<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrderArrivalQty
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);

        if (!is_null($data)) {
            if ($data == 0) {
                return $data;
            }

            $date = Mage::helper('core')->formatDate($row->getPoSupplyDate());
            $poNumber = $row->getPoNumber();
            $poUrl = $this->getUrl('Purchase/Orders/Edit', array('po_num' => $row->getPoId()));

            return "{$data}<br/>{$date}<br/>#<a href='{$poUrl}'>{$poNumber}</a>";
        }
        return $this->getColumn()->getDefault();
    }

    public function renderCss()
    {
        return parent::renderCss() . ' a-center';
    }
}