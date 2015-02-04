<?php
abstract class Soularpanic_TRSReports_Block_Adminhtml_Report_Grid_Abstract
    extends Mage_Adminhtml_Block_Report_Grid_Abstract {

    public function getCollection()
    {
        if (is_null($this->_collection)) {
            $this->setCollection(Mage::getModel('trsreports/reports_grouped_collection'));
        }
        return $this->_collection;
    }
//
//    public function addFieldToFilter($attribute, $condition = null) {
//        Mage::log("attribute: ".print_r($attribute, true)."\ncondition:".print_r($condition, true), null, 'trs_reports.log');
//    }

    public function getMultipleRows($item)
    {
        return null;
    }

    protected function _addCustomFilter($collection, $filterData)
    {
        $collection->setSortKey($filterData['sort']);
        $collection->setSortDir($filterData['dir']);
        $collection->setCustomFilterData($filterData);
        return $this;
    }
}