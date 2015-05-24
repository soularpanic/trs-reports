<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DailyMetric
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "DailyMetric";

    public function __construct() {
        $this->_headerText = Mage::helper('reports')->__('Daily Metrics Report');
        parent::__construct();
    }
}