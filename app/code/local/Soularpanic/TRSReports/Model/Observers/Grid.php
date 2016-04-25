<?php
class Soularpanic_TRSReports_Model_Observers_Grid {

    public function defaultToHiddenArchives(Varien_Event_Observer $observer) {
        if (Mage::app()->getRequest()->getRouteName() !== 'adminhtml') {
            return;
        }

        $collection = $observer->getEvent()->getCollection();
        $browseArchive = Mage::app()->getRequest()->getParam('show_archive', false);

        $collection->joinAttribute(
            'is_archived',
            'catalog_product/is_archived',
            'entity_id',
            null,
            'left',
            Mage::app()->getStore()
        );

        if ($browseArchive) {
            $filter = [
                ['attribute' => 'is_archived',
                    'eq' => '1']
            ];
        }
        else {
            $filter = [
                ['attribute' => 'is_archived',
                    'neq' => '1'],
                ['attribute' => 'is_archived',
                    'null' => true]
            ];
        }

        $collection->addAttributeToFilter($filter);
    }

    public function defaultTo200Records(Varien_Event_Observer $observer) {
        $block = $observer->getBlock();
        $blockClass = get_class($block);
        $blockParentClass = get_parent_class($block);

        if (Mage::app()->getRequest()->getRouteName() === 'adminhtml'
            && ($this->endsWith('_Grid', $blockClass)
                || $this->endsWith('_Grid', $blockParentClass))
            && !in_array($blockClass, $this->getOmittedGridClasses())) {
            $block->setDefaultLimit(200);
        }
    }

    /* Omitted from being defaulted to show 200 records */
    public function getOmittedGridClasses() {
        return [
            'Soularpanic_CmsmartAdminTheme_Block_Dashboard_Catalog_Product_Grid',
            'Soularpanic_CmsmartAdminTheme_Block_Dashboard_Customers_Grid',
            'Soularpanic_CmsmartAdminTheme_Block_Dashboard_Orders_Grid'
        ];
    }

    public function endsWith($needle, $haystack) {
        $_needleLen = strlen($needle);
        return ($_needleLen <= strlen($haystack))
        && substr($haystack, -1 * $_needleLen) === $needle;
    }

}