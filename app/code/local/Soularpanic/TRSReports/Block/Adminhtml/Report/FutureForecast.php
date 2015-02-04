<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_FutureForecast
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "FutureForecast";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Future Forecast');
        parent::__construct();
    }
}