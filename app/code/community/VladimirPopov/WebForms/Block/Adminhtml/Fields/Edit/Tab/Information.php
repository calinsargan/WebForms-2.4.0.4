<?php
/**
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Block_Adminhtml_Fields_Edit_Tab_Information
	extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout(){
		
		parent::_prepareLayout();
	}	
	
	protected function _prepareForm()
	{
		$model = Mage::getModel('webforms/fields');
		$form = new Varien_Data_Form();
		$form->setFieldNameSuffix('field');
		$form->setDataObject(Mage::registry('field'));

		$this->setForm($form);
		$fieldset = $form->addFieldset('webforms_form',array(
			'legend' => Mage::helper('webforms')->__('Information')
		));
		
		$fieldset->addField('name','text',array(
			'label' => Mage::helper('webforms')->__('Name'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'name'
		));

        $fieldset->addField('result_label','text',array(
			'label' => Mage::helper('webforms')->__('Result label'),
			'required' => false,
			'name' => 'result_label',
			'note' => Mage::helper('webforms')->__('Result label will be used on results page')
		));
		
		$fieldset->addField('code','text',array(
			'label' => Mage::helper('webforms')->__('Code'),
			'name' => 'code',
			'note' => Mage::helper('webforms')->__('Code is used to help identify this field in scripts'),
		));
		
		$fieldset->addField('type', 'select', array(
			'label'     => Mage::helper('webforms')->__('Type'),
			'title'     => Mage::helper('webforms')->__('Type'),
			'name'      => 'type',
			'required'  => false,
			'options'   => $model->getFieldTypes(),
		));
	
		$fieldsetsOptions  = Mage::registry('webforms_data')->getFieldsetsOptionsArray();
		if(count($fieldsetsOptions)>1){
			$fieldset->addField('fieldset_id', 'select', array(
				'label'     => Mage::helper('webforms')->__('Field set'),
				'title'     => Mage::helper('webforms')->__('Field set'),
				'name'      => 'fieldset_id',
				'required'  => false,
				'options'   => $fieldsetsOptions,
			));
		}
		
		$fieldset->addField('value','textarea',array(
			'label' => Mage::helper('webforms')->__('Field value(s)'),
			'required' => false,
			'name' => 'value',
			'note' => Mage::helper('webforms')->__('Select values should be separated with new line (start with ^ to check default).<br />Default values: <i>{{firstname}}, {{lastname}}, {{email}} etc</i> - logged in customer information<br/>Select/Contact values format:<br/><i>Name &lt;mailbox@mysite.com&gt;</i>'),
		));
		
		$fieldset->addField('email_subject', 'select', array(
			'label'     => Mage::helper('webforms')->__('Use as e-mail subject'),
			'title'     => Mage::helper('webforms')->__('Use as e-mail subject'),
			'name'      => 'email_subject',
			'note'		=> Mage::helper('webforms')->__('This field will be used as a subject in notification e-mail'),
			'required'  => false,
			'values'   => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('position','text',array(
			'label' => Mage::helper('webforms')->__('Position'),
			'required' => true,
			'name' => 'position',
			'note' => Mage::helper('webforms')->__('Field position in the form relative to field set'),
		));
		
		$fieldset->addField('required', 'select', array(
			'label'     => Mage::helper('webforms')->__('Required'),
			'title'     => Mage::helper('webforms')->__('Required'),
			'name'      => 'required',
			'required'  => false,
			'values'   => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('is_active', 'select', array(
			'label'     => Mage::helper('webforms')->__('Status'),
			'title'     => Mage::helper('webforms')->__('Status'),
			'name'      => 'is_active',
			'note'		=> Mage::helper('webforms')->__('If assigned field set is not active the field won`t be displayed'),
			'required'  => false,
			'options'   => Mage::getModel('webforms/webforms')->getAvailableStatuses(),
		));
		
		$form->addField('webform_id', 'hidden', array(
			'name'      => 'webform_id',
			'value'   => 1,
		));
		
		$form->addField('saveandcontinue','hidden',array(
			'name' => 'saveandcontinue'
		));

		Mage::dispatchEvent('webforms_adminhtml_fields_edit_tab_information_prepare_form', array('form' => $form, 'fieldset' => $fieldset));
		
		if (!$model->getId()) {
			$model->setData('is_active', '0');
		}
		
		if(Mage::getSingleton('adminhtml/session')->getWebFormsData())
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getWebFormsData());
			Mage::getSingleton('adminhtml/session')->setWebFormsData(null);
		} elseif(Mage::registry('field')){
			$form->setValues(Mage::registry('field')->getData());
		} 
		
		// set default field values
		if(!Mage::registry('field')->getId()){
			$form->setValues(array(
				'webform_id' => $this->getRequest()->getParam('webform_id'),
				'position' => 10
			));
		}
	
		return parent::_prepareForm();
	}
}  
?>
