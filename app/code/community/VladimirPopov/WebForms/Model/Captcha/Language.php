<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Captcha_Language extends Mage_Core_Model_Abstract{

	public function _construct()
	{
		parent::_construct();
		$this->_init('webforms/captcha_language');
	}
	
	public function toOptionArray(){
		return array(
			array('value' => 'en' , 'label' => Mage::helper('webforms')->__('English')),
			array('value' => 'nl' , 'label' => Mage::helper('webforms')->__('Dutch')),
			array('value' => 'fr' , 'label' => Mage::helper('webforms')->__('French')),
			array('value' => 'de' , 'label' => Mage::helper('webforms')->__('German')),
			array('value' => 'pt' , 'label' => Mage::helper('webforms')->__('Portuguese')),
			array('value' => 'ru' , 'label' => Mage::helper('webforms')->__('Russian')),
			array('value' => 'es' , 'label' => Mage::helper('webforms')->__('Spanish')),
			array('value' => 'tr' , 'label' => Mage::helper('webforms')->__('Turkish')),
		);
	}
	
}
?>
