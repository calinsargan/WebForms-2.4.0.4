<?php
class VladimirPopov_WebForms_Model_Webforms_Template
    extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('webforms/webforms_template');
    }

    public function toOptionArray()
    {
        return array(
            array('value' => 'webforms/default.phtml', 'label' => Mage::helper('webforms')->__('Default')),
            array('value' => 'webforms/legacy.phtml', 'label' => Mage::helper('webforms')->__('Legacy (Magento 1.3 and earlier)')),
            array('value' => 'webforms/ultimento.phtml', 'label' => Mage::helper('webforms')->__('Ultimento (theme matching template)')),
            array('value' => 'webforms/acumen.phtml', 'label' => Mage::helper('webforms')->__('Acumen (theme matching template)')),
        );
    }
}