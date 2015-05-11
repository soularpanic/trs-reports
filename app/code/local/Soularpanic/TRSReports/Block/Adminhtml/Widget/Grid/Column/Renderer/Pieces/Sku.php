<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_Pieces_Sku
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    const PRODUCT_INDICATOR = 'P';
    const LINE_INDICATOR = 'T';
    const PIECE_INDICATOR = 'L';

    public function render(Varien_Object $row) {
        $sku = $row->getData('derived_sku');
        $derivedId = $row->getData('derived_id');
        $idIndicator = substr($derivedId, 0, 1);

        if ($idIndicator !== self::PRODUCT_INDICATOR) {
            $html = "<em>$sku</em> ";
            if ($idIndicator === self::LINE_INDICATOR) {
                $html .= "(Line)";
            }
            if ($idIndicator === self::PIECE_INDICATOR) {
                $html .= "(Pieces)";
            }
        }
        else {
            $html = $sku;
        }
        return $html;
    }

}