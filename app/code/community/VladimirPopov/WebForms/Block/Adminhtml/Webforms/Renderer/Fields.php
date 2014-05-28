<?php
/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Block_Adminhtml_Webforms_Renderer_Fields extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $value = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id',$row->getId())->count();
        return $value;

    }
}