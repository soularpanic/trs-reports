<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_InTransitValue
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "InTransitValue";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('In Transit Value');
        parent::__construct();
    }
}