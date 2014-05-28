<?php
/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Block_Adminhtml_Webforms_Edit_Tab_Settings
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {

        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $model = Mage::getModel('webforms/webforms');
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('form');
        $form->setDataObject(Mage::registry('webforms_data'));

        $this->setForm($form);

        $fieldset = $form->addFieldset('webforms_general', array(
            'legend' => Mage::helper('webforms')->__('General Settings')
        ));

        $fieldset->addField('registered_only', 'select', array(
            'label' => Mage::helper('webforms')->__('Registered customers only'),
            'title' => Mage::helper('webforms')->__('Registered customers only'),
            'name' => 'registered_only',
            'required' => false,
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('survey', 'select', array(
            'label' => Mage::helper('webforms')->__('Survey mode'),
            'title' => Mage::helper('webforms')->__('Survey mode'),
            'name' => 'survey',
            'required' => false,
            'note' => Mage::helper('webforms')->__('Survey mode allows filling up the form only one time'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('approve', 'select', array(
            'label' => Mage::helper('webforms')->__('Enable approval'),
            'title' => Mage::helper('webforms')->__('Enable approval'),
            'name' => 'approve',
            'required' => false,
            'note' => Mage::helper('webforms')->__('Enable approval of results'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('redirect_url', 'text', array(
            'label' => Mage::helper('webforms')->__('Redirect URL'),
            'title' => Mage::helper('webforms')->__('Redirect URL'),
            'name' => 'redirect_url',
            'note' => Mage::helper('webforms')->__('Redirect to specified url after successful submission'),
        ));

        $fieldset = $form->addFieldset('webforms_email', array(
            'legend' => Mage::helper('webforms')->__('E-mail Settings')
        ));

        $fieldset->addField('send_email', 'select', array(
            'label' => Mage::helper('webforms')->__('Send results by e-mail to admin'),
            'title' => Mage::helper('webforms')->__('Send results by e-mail to admin'),
            'name' => 'send_email',
            'required' => false,
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'note' => Mage::helper('webforms')->__('Enable admin notifications. If you have Select/Contact field in the form, e-mail notification will be sent twice: to admin and to selected contact')
        ));

        $fieldset->addField('duplicate_email', 'select', array(
            'label' => Mage::helper('webforms')->__('Duplicate results by e-mail to customer'),
            'title' => Mage::helper('webforms')->__('Duplicate results by e-mail to customer'),
            'note' => Mage::helper('webforms')->__('Enable customer notifications'),
            'name' => 'duplicate_email',
            'required' => false,
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('webforms')->__('Notification e-mail address'),
            'note' => Mage::helper('webforms')->__('If empty default notification e-mail address will be used. You can set multiple addresses comma-separated'),
            'name' => 'email'
        ));

        $fieldset->addField('email_reply_to', 'text', array(
            'label' => Mage::helper('webforms')->__('Reply-to address for customer'),
            'note' => Mage::helper('webforms')->__('Set reply-to parameter in customer notifications'),
            'name' => 'email_reply_to'
        ));

        $fieldset->addField('add_header', 'select', array(
            'label' => Mage::helper('webforms')->__('Add header to the message'),
            'title' => Mage::helper('webforms')->__('Add header to the message'),
            'name' => 'add_header',
            'note' => Mage::helper('webforms')->__('Add header with Store Group, IP and other information to the message'),
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));


        // $fieldset = $form->addFieldset('webforms_captcha', array(
        //     'legend' => Mage::helper('webforms')->__('reCaptcha Settings')
        // ));

        // $fieldset->addField('captcha_mode', 'select', array(
        //     'label' => Mage::helper('webforms')->__('Captcha mode'),
        //     'title' => Mage::helper('webforms')->__('Captcha mode'),
        //     'name' => 'captcha_mode',
        //     'required' => false,
        //     'note' => Mage::helper('webforms')->__('Default value is set in Forms Settings'),
        //     'values' => Mage::getModel('webforms/captcha_mode')->toOptionArray(true),
        // ));


        Mage::dispatchEvent('webforms_adminhtml_webforms_edit_tab_settings_prepare_form', array('form' => $form, 'fieldset' => $fieldset));

        if (!Mage::registry('webforms_data')->getId()) {
            Mage::registry('webforms_data')->setData('send_email', 1);
        }

        if (Mage::getSingleton('adminhtml/session')->getWebFormsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getWebFormsData());
            Mage::getSingleton('adminhtml/session')->setWebFormsData(null);
        } elseif (Mage::registry('webforms_data')) {
            $form->setValues(Mage::registry('webforms_data')->getData());
        }

        return parent::_prepareForm();
    }
}

?>
