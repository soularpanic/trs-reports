<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_DeliveryAndValueDeliveryDetail
    extends Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract {

    protected $_reportTag = "DeliveryAndValueDeliveryDetail";

    public function __construct()
    {
        $this->_headerText = Mage::helper('reports')->__('Delivery And Value Delivery Detail');
        parent::__construct();
        $this->_removeButton("filter_form_submit");
    }
}