<?php
abstract class Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Container_Abstract
extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup = "trs";
    protected $_reportTag = null;

    public function __construct() {
        $this->_controller = "adminhtml_report_{$this->_reportTag}";
        $this->setTemplate('report/grid/container.phtml');
        parent::__construct();
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        $action = strtolower($this->_reportTag);
        return $this->getUrl("*/*/{$action}", array('_current' => true));
    }
}