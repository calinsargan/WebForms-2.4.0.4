<?php
/**
 * @author         Vladimir Popov
 * @copyright      Copyright (c) 2014 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Webforms
    extends Mage_Core_Model_Abstract
{

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_fields_to_fieldsets = array();
    protected $_hidden = array();

    public function _getFieldsToFieldsets()
    {
        return $this->_fields_to_fieldsets;
    }

    public function _setFieldsToFieldsets($fields_to_fieldsets)
    {
        $this->_fields_to_fieldsets = $fields_to_fieldsets;
        return $this;
    }

    public function _getHidden()
    {
        return $this->_hidden;
    }

    public function _setHidden($hidden)
    {
        $this->_hidden = $hidden;
        return $this;
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('webforms/webforms');
    }

    public function getAvailableStatuses()
    {
        $statuses = new Varien_Object(array(
            self::STATUS_ENABLED => Mage::helper('webforms')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('webforms')->__('Disabled'),
        ));

        Mage::dispatchEvent('webforms_statuses', array('statuses' => $statuses));

        return $statuses->getData();

    }

    public function toOptionArray()
    {
        $collection = $this->getCollection()->addFilter('is_active', self::STATUS_ENABLED)->addOrder('name', 'asc');
        $option_array = array();
        foreach ($collection as $webform)
            $option_array[] = array('value' => $webform->getId(), 'label' => $webform->getName());
        return $option_array;
    }

    public function getFieldsetsOptionsArray()
    {
        $collection = Mage::getModel('webforms/fieldsets')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getId());
        $collection->getSelect()->order('position asc');
        $options = array(0 => '...');
        foreach ($collection as $o) {
            $options[$o->getId()] = $o->getName();
        }
        return $options;
    }

    public function getTemplatesOptions()
    {
        $options = array(0 => Mage::helper('webforms')->__('Default'));
        $templates = Mage::getResourceSingleton('core/email_template_collection');
        foreach ($templates as $template) {
            $options[$template->getTemplateId()] = $template->getTemplateCode();
        }
        return $options;
    }

    public function getEmailSettings()
    {
        $settings["email_enable"] = $this->getSendEmail();
        $settings["email"] = Mage::getStoreConfig('webforms/email/email');
        if ($this->getEmail())
            $settings["email"] = $this->getEmail();
        return $settings;
    }

    public function getFieldsToFieldsets($all = false)
    {
        //get form fieldsets
        $fieldsets = Mage::getModel('webforms/fieldsets')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getId());

        if (!$all)
            $fieldsets->addFilter('is_active', self::STATUS_ENABLED);

        $fieldsets->getSelect()->order('position asc');

        //get form fields
        $fields = Mage::getModel('webforms/fields')
            ->setStoreId($this->getStoreId())
            ->getCollection()
            ->addFilter('webform_id', $this->getId());

        if (!$all) {
            $fields->addFilter('is_active', self::STATUS_ENABLED);
        }

        $fields->getSelect()->order('position asc');

        //fields to fieldsets
        //make zero fieldset
        $fields_to_fieldsets = array();
        $hidden = array();
        $required_fields = array();
        $default_data = array();

        foreach ($fields as $field) {
            // set default data
            if (strstr($field->getType(), 'select')) {
                $options = $field->getOptionsArray();
                $checked_options = array();
                foreach ($options as $o) {
                    if ($o['checked']) {
                        $checked_options[] = $o['value'];
                    }
                }
                if (count($checked_options)) {
                    $default_data[$field->getId()] = $checked_options;
                }
            }

            if ($field->getFieldsetId() == 0) {
                if ($all || $field->getType() != 'hidden') {
                    if ($field->getRequired()) $required_fields[] = 'field_' . $field->getId();
                    if ($all || $field->getIsActive())
                        $fields_to_fieldsets[0]['fields'][] = $field;
                } elseif ($field->getType() == 'hidden') {
                    $hidden[] = $field;
                }
            }
        }


        foreach ($fieldsets as $fieldset) {
            foreach ($fields as $field) {
                if ($field->getFieldsetId() == $fieldset->getId()) {
                    if ($all || $field->getType() != 'hidden') {
                        if ($all || $field->getIsActive())
                            $fields_to_fieldsets[$fieldset->getId()]['fields'][] = $field;
                    } elseif ($field->getType() == 'hidden') {
                        if ($all || $field->getIsActive())
                            $hidden[] = $field;
                    }
                }
            }
            if (!empty($fields_to_fieldsets[$fieldset->getId()]['fields'])) {
                $fields_to_fieldsets[$fieldset->getId()]['name'] = $fieldset->getName();
                $fields_to_fieldsets[$fieldset->getId()]['result_display'] = $fieldset->getResultDisplay();
            }
        }

        $this->_setFieldsToFieldsets($fields_to_fieldsets);
        $this->_setHidden($hidden);

        return $fields_to_fieldsets;

    }

    public function useCaptcha()
    {
        $useCaptcha = true;
        if ($this->getCaptchaMode() != 'default') {
            $captcha_mode = $this->getCaptchaMode();
        } else {
            $captcha_mode = Mage::getStoreConfig('webforms/captcha/mode');
        }
        if ($captcha_mode == "off" || !Mage::helper('webforms')->captchaAvailable())
            $useCaptcha = false;
        if (Mage::getSingleton('customer/session')->getCustomerId() && $captcha_mode == "auto")
            $useCaptcha = false;
        if ($this->getData('disable_captcha'))
            $useCaptcha = false;

        return $useCaptcha;
    }


    public function validatePostResult()
    {
        $postData = $this->getPostData();

        if (Mage::registry('webforms_errors_flag_' . $this->getId())) return Mage::registry('webforms_errors_' . $this->getId());

        $errors = array();

        // check captcha
        if ($this->useCaptcha()) {
            if (Mage::app()->getRequest()->getPost('recaptcha_response_field')) {
                $verify = Mage::helper('webforms')->getCaptcha()->verify(Mage::app()->getRequest()->getPost('recaptcha_challenge_field'), Mage::app()->getRequest()->getPost('recaptcha_response_field'));
                if (!$verify->isValid()) {
                    $errors[] = Mage::helper('webforms')->__('Verification code was not correct. Please try again.');
                }
            } else {
                $errors[] = Mage::helper('webforms')->__('Verification code was not correct. Please try again.');
            }
        }

        // check  validation
        $fields_to_fieldsets = $this->getFieldsToFieldsets();
        foreach ($fields_to_fieldsets as $fieldset_id => $fieldset)
            foreach ($fieldset['fields'] as $field) {

                if ($field->getRequired() && is_array($this->getPostData())) {
                    foreach ($this->getPostData() as $key => $value) {
                        if (
                            $key == $field->getId()
                            &&
                            $field->getType() != 'select/checkbox'
                            &&
                            trim($value) == ''
                        ) {
                            $errors[] = Mage::helper('webforms')->__('%s is required', $field->getName());
                        }
                    }
                }
                // check e-mail stoplist
                if ($field->getIsActive() && $field->getType() == 'email') {
                    if (!empty($postData[$field->getId()])) {
                        if (stristr(Mage::getStoreConfig('webforms/email/stoplist'), $postData[$field->getId()])) {
                            $errors[] = Mage::helper('webforms')->__('E-mail address is blocked: %s', $postData[$field->getId()]);
                        }
                    }
                }
            }

        $validate = new Varien_Object(array('errors' => $errors));

        Mage::dispatchEvent('webforms_validate_post_result', array('webform' => $this, 'validate' => $validate));

        Mage::register('webforms_errors_flag_' . $this->getId(), true);
        Mage::register('webforms_errors_' . $this->getId(), $validate->getData('errors'));

        return $validate->getData('errors');
    }


    public function savePostResult($config = array())
    {
        try {
            $postData = Mage::app()->getRequest()->getPost();
            if (!empty($config['prefix'])) {
                $postData = Mage::app()->getRequest()->getPost($config['prefix']);
            }
            $result = Mage::getModel('webforms/results');
            $new_result = true;
            if (!empty($postData['result_id'])) {
                $new_result = false;
                $result->load($postData['result_id'])->addFieldArray();
            }

            $this->setData('post_data', $postData['field']);

            $errors = $this->validatePostResult();

            if (count($errors)) {
                foreach ($errors as $error) {
                    Mage::getSingleton('core/session')->addError($error);
                    Mage::getSingleton('core/session')->setData('webform_result_tmp_' . $this->getId(), $postData);
                }
                return false;
            }

            Mage::getSingleton('core/session')->setData('webform_result_tmp_' . $this->getId(), false);

            $iplong = ip2long(Mage::helper('webforms')->getRealIp());

            $approve = 1;
            if($this->getApprove()) $approve = 0;

            $result->setData('field', $postData['field'])
                ->setWebformId($this->getId())
                ->setStoreId(Mage::app()->getStore()->getId())
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setCustomerIp($iplong)
                ->setApproved($approve)
                ->save();

            $fields = Mage::getModel('webforms/fields')
                ->setStoreId($this->getStoreId())
                ->getCollection()
                ->addFilter('webform_id', $this->getId());

            Mage::dispatchEvent('webforms_result_submit', array('result' => $result, 'webform' => $this));

            // send e-mail

            if ($new_result) {

                $emailSettings = $this->getEmailSettings();

                $result = Mage::getModel('webforms/results')->load($result->getId());

                // send admin notification
                if ($emailSettings['email_enable']) {
                    $result->sendEmail();
                }

                // send customer notification
                if ($this->getDuplicateEmail()) {
                    $result->sendEmail('customer');
                }

            }


            return $result->getId();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            return false;
        }
    }

    public function getSubmitButtonText()
    {
        $submit_button_text = trim($this->getData('submit_button_text'));
        if (strlen($submit_button_text) == 0)
            $submit_button_text = 'Submit';
        return $submit_button_text;
    }
}