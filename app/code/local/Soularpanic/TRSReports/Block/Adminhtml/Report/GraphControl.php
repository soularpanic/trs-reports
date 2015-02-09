<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_GraphControl
    extends Mage_Adminhtml_Block_Widget {

    const DATE_FORMAT = 'Y-m-d';
    const DEFAULT_GRANULARITY = 'day';
    protected $SECONDS_PER_DAY;

    function __construct() {
        parent::__construct();

        $this->SECONDS_PER_DAY = 1 /* day */
            * 24 /* hours/day */
            * 60 /* mins/hour */
            * 60 /* secs/min */;

        $this->setFromDaysAgoDefault(1);
    }

    public function getFromDate() {
        $sessionFrom = Mage::getSingleton('core/session')->getTrsReportFromDate();
        $from = $sessionFrom ? $sessionFrom : date(self::DATE_FORMAT, (time() - ($this->getFromDaysAgoDefault() * $this->SECONDS_PER_DAY)));
        return $from;
    }

    public function getToDate() {
        $sessionTo = Mage::getSingleton('core/session')->getTrsReportToDate();

        $to = $sessionTo ? $sessionTo : date(self::DATE_FORMAT, time());
        return $to;
    }

    public function getGranularity() {
        $sessionGranularity = Mage::getSingleton('core/session')->getTrsReportGranularity();
        $granularity = $sessionGranularity ? $sessionGranularity : self::DEFAULT_GRANULARITY;
        return $granularity;
    }
}
