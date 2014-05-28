<?php
class VladimirPopov_WebForms_Block_Adminhtml_Results_Renderer_Id extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		$html = $value;
		return $html;
	}

}
?>
