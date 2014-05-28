<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Block_Adminhtml_Fieldsets_Edit
	extends Mage_Adminhtml_Block_Widget_Form_Container
{
	
	protected function _prepareLayout(){

		parent::_prepareLayout();

	}
	
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'webforms';
		$this->_controller = 'adminhtml_fieldsets';

		$this->_addButton('saveandcontinue', array(
			'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'   => "$('saveandcontinue').value = true; editForm.submit()",
			'class'     => 'save',
		), -100);
	}
	
	public function getSaveUrl()
	{
		return $this->getUrl('*/adminhtml_webforms/save',array('webform_id'=>Mage::registry('webforms_data')->getId()));
	}
	
	public function getBackUrl(){
		return $this->getUrl('*/adminhtml_webforms/edit',array('id'=>Mage::registry('webforms_data')->getId()));
	}

	public function getHeaderText()
	{
		if(!is_null(Mage::registry('fieldsets_data')->getId())) {
			return Mage::helper('webforms')->__("Edit Field Set '%s'", $this->htmlEscape(Mage::registry('fieldsets_data')->getName()));
		} else {
			return Mage::helper('webforms')->__('New Field Set');
		}
	}

	public function getFormHtml()
	{
		$html = parent::getFormHtml();

		return $html;
	}
}  
?>
