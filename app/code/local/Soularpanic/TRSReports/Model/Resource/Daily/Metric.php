<?php
class Soularpanic_TRSReports_Model_Resource_Daily_Metric
    extends Mage_Core_Model_Resource_Db_Abstract {

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('trsreports/daily_metric', 'entity_id');
    }
}