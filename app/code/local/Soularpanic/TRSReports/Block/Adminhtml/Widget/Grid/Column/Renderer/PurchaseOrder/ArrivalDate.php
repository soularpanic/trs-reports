<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrder_ArrivalDate
    extends Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_PurchaseOrder_Abstract {
    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);

        if (!is_null($data)) {
            $html = '';
            foreach ($data as $po) {
                $qty = '';
                if ($this->getShowQuantity()) {
                    $qty.= "{$row->getQtyIncoming()}<br/>";
                }
                $date = Mage::helper('core')->formatDate($po->getPoExpectedArrival());
                $poNumber = $po->getPoCode();
                $poUrl = $this->getUrl('Purchase/Orders/Edit', array('po_num' => $po->getPoId()));

                if ($html) {
                    $html.= "<br/>";
                }
                $html.= "{$qty}{$date}<br/>#<a href='{$poUrl}'>{$poNumber}</a>";
            }
            return $html;
        }
        return $this->getColumn()->getDefault();
    }

    public function renderCss()
    {
        return parent::renderCss() . ' a-center';
    }
}