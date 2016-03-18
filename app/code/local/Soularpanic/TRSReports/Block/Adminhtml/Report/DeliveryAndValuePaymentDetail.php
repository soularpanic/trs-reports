<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValuePaymentDetail
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "DeliveryAndValuePaymentDetail";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Delivery And Value Payment Detail');
        parent::__construct();
        $this->_removeButton("filter_form_submit");
    }
}