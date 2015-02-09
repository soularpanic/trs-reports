<?php
abstract class Soularpanic_TRSReports_Block_Adminhtml_Report_JsonGrid_Abstract
extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setTemplate('trsreports/JsonGrid.phtml');
        $this->setRowClickCallback('openGridRow');
        $this->_emptyText = Mage::helper('adminhtml')->__('No records found.');
    }

    public function getCollection() {
        return true;
    }

    public function getPagerVisibility() {
        return false;
    }

    protected function _prepareCollection() {
        return this;
    }

    public function getCountTotals() {
        return false;
    }

    public function getSubTotals() {
        return -1;
    }
}