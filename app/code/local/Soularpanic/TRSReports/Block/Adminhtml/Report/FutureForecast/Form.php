<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_FutureForecast_Form
    extends Mage_Adminhtml_Block_Report_Filter_Form {
    protected function _prepareForm() {
        parent::_prepareForm();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->addField("growth_percent", 'text', array(
            'name' => 'growth_percent',
            'label' => $this->__("Growth Percent"),
            'title' => $this->__("Growth Percent")
        ));
        $fieldset->addField('future', 'date', array(
            'name'      => 'future',
            'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'label'     => Mage::helper('reports')->__('Future'),
            'title'     => Mage::helper('reports')->__('Future'),
            'required'  => true
        ));
    }
}