<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Captcha_Theme extends Mage_Core_Model_Abstract{

	public function _construct()
	{
		parent::_construct();
		$this->_init('webforms/captcha_theme');
	}
	
	public function toOptionArray(){
		return array(
			array('value' => 'red' , 'label' => Mage::helper('webforms')->__('Red')),
			array('value' => 'white' , 'label' => Mage::helper('webforms')->__('White')),
			array('value' => 'blackglass' , 'label' => Mage::helper('webforms')->__('Blackglass')),
			array('value' => 'clean' , 'label' => Mage::helper('webforms')->__('Clean')),
		);
	}
	
}
?>
