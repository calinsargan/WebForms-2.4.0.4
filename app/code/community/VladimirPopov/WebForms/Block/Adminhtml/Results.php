<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Block_Adminhtml_Results extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'adminhtml_results';
		$this->_blockGroup = 'webforms';
		$webform = Mage::getModel('webforms/webforms')
			->setStoreId($this->getRequest()->getParam('store'))
			->load($this->getRequest()->getParam('webform_id'));
		if(!Mage::registry('webform_data')){
			Mage::register('webform_data',$webform);
		}
		$this->_headerText = $webform->getName();
		parent::__construct();
		$this->_removeButton('add');
		$this->_addButton('edit', array(
			'label'     => Mage::helper('webforms')->__('Edit Form'),
			'onclick'   => 'setLocation(\'' . $this->getEditUrl() .'\')',
			'class'     => 'edit',
		));
	}
	
	public function getEditUrl(){
		return $this->getUrl('*/adminhtml_webforms/edit',array('id'=>$this->getRequest()->getParam('webform_id')));
	}
}  
?>
