<?php
class Soularpanic_TRSReports_Block_Adminhtml_Report_Manage_Lines_Form_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('display', [
            'legend' => 'Product Line Details',
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('entity_id', 'hidden', [
            'name' => 'entity_id'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => 'Name'
        ]);

        $fieldset->addField('line_sku', 'text', [
            'name' => 'line_sku',
            'label' => 'SKU'
        ]);

        if (Mage::registry('soularpanic_adminform_manage_lines')) {
            $form->setValues(Mage::registry('soularpanic_adminform_manage_lines')->getData());
        }

        return parent::_prepareForm();
    }

}