<?php
class Soularpanic_TRSReports_Model_Observers_Grid {

    public function defaultToHiddenArchives(Varien_Event_Observer $observer) {
        $req = Mage::app()->getRequest();
        $collection = $observer->getEvent()->getCollection();

    }

    public function defaultTo200Records(Varien_Event_Observer $observer) {
        $y = $observer;
        $req = Mage::app()->getRequest();
        $req->getRouteName(); // if it's admin or adminhtml
    }

}