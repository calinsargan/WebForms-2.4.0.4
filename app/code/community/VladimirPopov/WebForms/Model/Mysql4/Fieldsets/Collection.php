<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Mysql4_Fieldsets_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	public function _construct(){
		parent::_construct();
		$this->_init('webforms/fieldsets');
	}

	protected function _afterLoad()
	{
		return parent::_afterLoad();
	}
}  
?>
