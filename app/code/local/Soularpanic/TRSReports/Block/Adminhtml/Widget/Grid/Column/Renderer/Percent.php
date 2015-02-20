<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_Percent
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = round((float)$data, 4) * 100;
            $sign = (bool)(int)$this->getColumn()->getShowNumberSign() && ($value > 0) ? '+' : '';
            if ($sign) {
                $value = $sign . $value;
            }
            return ($value ? $value : '0').'%'; // fixed for showing zero in grid
        }
        return $this->getColumn()->getDefault();
    }

    public function renderCss()
    {
        return parent::renderCss() . ' a-right';
    }
}