<?php
class Soularpanic_TRSReports_Block_Adminhtml_Catalog_Product_Tree_Manage_Form_Edit_Form
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
            'legend' => 'Product Tree Details',
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('entity_id', 'hidden', [
            'name' => 'entity_id'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => 'Name'
        ]);

        $fieldset->addField('sku', 'text', [
            'sku' => 'sku',
            'label' => 'SKU'
        ]);


        if (Mage::registry('soularpanic_adminform_manage_product_trees')) {
            $form->setValues(Mage::registry('soularpanic_adminform_manage_product_trees')->getData());
        }

        return parent::_prepareForm();
    }

}