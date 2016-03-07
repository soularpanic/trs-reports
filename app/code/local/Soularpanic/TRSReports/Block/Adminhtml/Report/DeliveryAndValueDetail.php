<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValueDetail
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "DeliveryAndValueDetail";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Delivery And Value Detail');
        parent::__construct();
        $this->_removeButton("filter_form_submit");
    }
}