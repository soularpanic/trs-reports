<?php
class Soularpanic_TRSReports_Helper_SqlDate
extends Soularpanic_TRSReports_Helper_Data {

    public function convertFromDate($dateStr) {
        $dateObj = Mage::getSingleton('core/locale')->utcDate(null, $dateStr, true, 'YYYY-MM-dd');
        $convertedDateStr = $dateObj->toString('YYYY-MM-dd hh:mm:ss');
        return $convertedDateStr;
    }

    public function convertToDate($dateStr) {
        $dateObj = Mage::getSingleton('core/locale')->utcDate(null, $dateStr, true, 'YYYY-MM-dd');
        $dateObj->add('23:59:59', Zend_Date::TIMES);
        $convertedDateStr = $dateObj->toString('YYYY-MM-dd hh:mm:ss');
        return $convertedDateStr;
    }

}