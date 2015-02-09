<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_GraphControl_StoreSelector
    extends Mage_Adminhtml_Block_Widget {

    const DATE_FORMAT = 'Y-m-d';
    const DEFAULT_GRANULARITY = 'day';
    protected $SECONDS_PER_DAY;

    public function getStores() {
        return Mage::getModel('adminhtml/system_store')->getStoreOptionHash(true);
    }

    public function getSelectedStoreIds() {
        $sessionStoreIds = Mage::getSingleton('core/session')->getTrsReportStoreIds();
        $storeIds = $sessionStoreIds ? explode(',', $sessionStoreIds) : array();
        return $storeIds;
    }
}
