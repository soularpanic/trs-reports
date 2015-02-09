<?php
class Soularpanic_TRSReports_Model_Reports_Grouped_Collection
    extends Mage_Reports_Model_Grouped_Collection {

    public function addFieldToFilter($field, $cond) {
        $this->_resourceCollection->addFieldToFilter($field, $cond);
    }

}