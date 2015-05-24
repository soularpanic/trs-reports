<?php
class Soularpanic_TRSReports_Model_Resource_Daily_Metric_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('trsreports/daily_metric');
    }

}