<?php
abstract class Soularpanic_TRSReports_Controller_Abstract
    extends Mage_Adminhtml_Controller_Report_Abstract {

    public function log($message) {
        Mage::helper('trsreports')->log($message);
    }

}