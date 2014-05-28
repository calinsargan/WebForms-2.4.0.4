<?php
class VladimirPopov_WebForms_Model_Captcha extends Zend_Service_ReCaptcha{

	public function getHtml(){
		if(Mage::getStoreConfig('webforms/captcha/api') != 'ajax')
			return parent::getHtml();
		
		$return = "";
		
		$div_id = "webform_recaptcha";
		if(Mage::registry('webform')){
			$div_id = "webform_".Mage::registry('webform')->getId()."_recaptcha";
		}
		
		$return .= <<<HTML
		<div id="{$div_id}"></div>
HTML;

		$return .= <<<SCRIPT
<script type="text/javascript" src="https://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
SCRIPT;
		
		if (!empty($this->_options)) {
			$encoded = Zend_Json::encode($this->_options);
		}
		
		$return .= <<<SCRIPT
<script type="text/javascript">
	Recaptcha.create("{$this->_publicKey}", "{$div_id}", {$encoded});
</script>
SCRIPT;
		
		return $return;
	}
}
?>
