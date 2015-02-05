<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrderArrivalDate
extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);

        if (!is_null($data)) {
            $qty = '';
            if ($this->getShowQuantity()) {
                $qty.= "{$row->getQtyIncoming()}<br/>";
            }
            $date = Mage::helper('core')->formatDate($data);
            $poNumber = $row->getPoNumber();
            $poUrl = $this->getUrl('Purchase/Orders/Edit', array('po_num' => $row->getPoId()));

            return "{$qty}{$date}<br/>#<a href='{$poUrl}'>{$poNumber}</a>";
        }
        return $this->getColumn()->getDefault();
    }

    public function renderCss()
    {
        return parent::renderCss() . ' a-center';
    }
}