<?php
class Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_FutureForecast_FutureQtyWithCalculations
extends Soularpanic_TRSReports_Block_Adminhtml_Widget_Grid_Column_Renderer_FlooredDecimal {

    protected function _getValue(Varien_Object $row) {
        $qtyValue = parent::_getValue($row);
        $qtySold = $row->getTotalQtySold();
        $start = $row->getStart();
        $end = $row->getEnd();
        $periodTime = $row->getTimeInDays();
        $futurePeriod = $row->getFutureTimeInDays();
        $growthRate = $row->getGrowthRate();
        $calculations = "Period Start: $start\nPeriod End: $end\nPeriod Time: $periodTime\nProjected Time: $futurePeriod\nQty Sold: $qtySold\n$growthRate * ($qtySold / $periodTime) * $futurePeriod";
        $html = "<a href='#' title='$calculations'>$qtyValue</a>";
        return $html;
    }

}