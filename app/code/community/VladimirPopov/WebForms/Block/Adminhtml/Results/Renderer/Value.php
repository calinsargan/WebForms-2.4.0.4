<?php
/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Block_Adminhtml_Results_Renderer_Value extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $field_id = str_replace('field_', '', $this->getColumn()->getIndex());
        $field = Mage::getModel('webforms/fields')->load($field_id);
        $value = $row->getData($this->getColumn()->getIndex());
        $html = '';
        if ($field->getType() == 'textarea') {
            $html = $this->getTextareaBlock($row);
        }
        if ($field->getType() == 'email') {
            if($value){
                $websiteId = Mage::app()->getStore($row->getStoreId())->getWebsite()->getId();
                $customer = Mage::getModel('customer/customer')->setData('website_id',$websiteId)->loadByEmail($value);
                $html = $value;
                if($customer->getId()){
                    $html.= " [<a href='" . $this->getCustomerUrl($customer->getId()) . "' target='_blank'>" . $customer->getName() . "</a>]";
                }
            }
        }

        $html_object = new Varien_Object(array('html' => $html));

        Mage::dispatchEvent('webforms_block_adminhtml_results_renderer_value_render', array('field' => $field, 'html_object' => $html_object, 'value' => $value));

        if ($html_object->getHtml())
            return $html_object->getHtml();

        return nl2br(htmlspecialchars($value));
    }

    public function getTextareaBlock(Varien_Object $row)
    {
        $field_id = str_replace('field_', '', $this->getColumn()->getIndex());
        $value = htmlspecialchars($row->getData($this->getColumn()->getIndex()));
        if (strlen($value) > 200 || substr_count($value, "\n") > 11) {
            $div_id = 'x_' . $field_id . '_' . $row->getId();
            $onclick = "Effect.toggle('$div_id', 'slide', { duration: 0.3 }); this.style.display='none';  return false;";
            $pos = strpos($value, "\n", 200);
            if ($pos > 300 || !$pos)
                $pos = strpos($value, " ", 200);
            if ($pos > 300)
                $pos = 200;
            if (!$pos) $pos = 200;
            $html = '<div>' . nl2br(substr($value, 0, $pos)) . '</div>';
            $html .= '<div id="' . $div_id . '" style="display:none">' . nl2br(substr($value, $pos, strlen($value))) . '<br/></div>';
            $html .= '<a onclick="' . $onclick . '" style="text-decoration:none;float:right">[' . $this->__('Expand') . ']</a>';
            return $html;
        }
        return nl2br($value);
    }

    public function getCustomerUrl($customerId)
    {

        return $this->getUrl('adminhtml/customer/edit', array('id' => $customerId, '_current' => false));
    }

}

?>
