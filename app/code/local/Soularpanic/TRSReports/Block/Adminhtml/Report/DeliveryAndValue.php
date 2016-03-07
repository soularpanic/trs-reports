<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValue
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "DeliveryAndValue";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Delivery And Value');
        parent::__construct();
    }
}