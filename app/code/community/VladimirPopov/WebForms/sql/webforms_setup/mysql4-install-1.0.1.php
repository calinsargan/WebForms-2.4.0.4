<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$edition = 'CE';
$version = explode('.', Mage::getVersion());
if ($version[1] >= 9)
	$edition = 'EE';

$webforms_table = 'webforms';
if((float)substr(Mage::getVersion(),0,3)>1.1 || $edition == 'EE')
	$webforms_table = $this->getTable('webforms/webforms');

// Magento 1.6 compatibility
$REM = "";
if((float)substr(Mage::getVersion(),0,3)>=1.6 && method_exists($installer->getConnection(), 'dropTable')){
	$REM = "--";
	$REM = "--";
	$installer->getConnection()->dropTable($this->getTable('webforms/webforms'));
	$installer->getConnection()->dropTable($this->getTable('webforms/fields'));
	$installer->getConnection()->dropTable($this->getTable('webforms/fieldsets'));
	$installer->getConnection()->dropTable($this->getTable('webforms/results'));
	$installer->getConnection()->dropTable($this->getTable('webforms/results_values'));
}

$installer->run("
$REM DROP TABLE IF EXISTS `$webforms_table`;
CREATE TABLE IF NOT EXISTS `$webforms_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `success_text` text NOT NULL,
  `registered_only` tinyint(1) NOT NULL,
  `send_email` tinyint(1) NOT NULL,
  `duplicate_email` tinyint(1) NOT NULL,
  `email` varchar(255) NOT NULL,
  `survey` tinyint(1) NOT NULL,
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

$REM DROP TABLE IF EXISTS `{$this->getTable('webforms/fields')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('webforms/fields')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_id` int(11) NOT NULL,
  `fieldset_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `size` varchar(20) NOT NULL,
  `value` text NOT NULL,
  `email_subject` tinyint(1) NOT NULL,
  `css_class` varchar(255) NOT NULL,
  `css_style` varchar(255) NOT NULL,
  `position` int(11) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `created_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

$REM DROP TABLE IF EXISTS `{$this->getTable('webforms/fieldsets')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('webforms/fieldsets')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_id` int(11) NOT NULL,
  `name` varchar(100)  NOT NULL,
  `position` int(11) NOT NULL,
  `created_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

$REM DROP TABLE IF EXISTS `{$this->getTable('webforms/results')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('webforms/results')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webform_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_ip` int(11) NOT NULL,
  `created_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

$REM DROP TABLE IF EXISTS `{$this->getTable('webforms/results_values')}`;
CREATE TABLE IF NOT EXISTS `{$this->getTable('webforms/results_values')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `result_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` text  NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `result_id` (`result_id`,`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

// Magento 1.6 compatibility

if((float)substr(Mage::getVersion(),0,3)<=1){
	$installer->run("
	INSERT INTO `$webforms_table` VALUES(1, 'Contact Us', '<p>You can use this extension to create and manage web-forms on your sites.</p>', '<p>Thank you for contacting me.</p>\r\n<p>You should get notification e-mail to the address you specified in the form.</p>\r\n<p>If you wonder how survey results are proccessed. Its exported to Excel XML file from administration panel, then analyzed in Excel application.</p>', 0, 1, 1, '', 0, '2011-06-27 09:54:10', '2011-06-28 04:28:04', 1);

	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(1, 1, 1, 'First name', 'text', 'standard', '{{firstname}}', 0, 'validate-alpha', '', 10, 1, '2011-06-27 09:58:43', '2011-06-27 10:03:03', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(2, 1, 1, 'Last name', 'text', 'standard', '{{lastname}}', 0, 'validate-alpha', '', 20, 1, '2011-06-27 10:16:03', '2011-06-27 10:16:03', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(3, 1, 1, 'E-mail address', 'email', 'wide', '{{email}}', 0, '', '', 30, 1, '2011-06-27 10:16:43', '2011-06-27 10:16:43', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(4, 1, 2, 'Do you need custom web-forms on your site?', 'select/radio', 'standard', 'Yes\r\nNo', 0, '', '', 40, 0, '2011-06-27 10:18:29', '2011-06-28 03:51:42', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(5, 1, 2, 'Did you try other web-forms extensions?', 'select/radio', 'standard', 'Yes\r\nNo', 0, '', '', 50, 0, '2011-06-27 10:19:40', '2011-06-28 03:52:23', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(6, 1, 2, 'How did you find this page?', 'select/checkbox', 'standard', 'Search engine\r\nMagento connect\r\nForums\r\nBlogs\r\nFriends\r\nLink from the other site', 0, '', '', 70, 1, '2011-06-27 10:24:46', '2011-06-28 03:53:32', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(7, 1, 2, 'What other web-forms extensions have you tried?', 'text', 'wide', '', 0, '', '', 60, 0, '2011-06-27 10:28:39', '2011-06-28 04:25:04', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(8, 1, 3, 'Subject', 'select', 'wide', 'I like your extension\r\nI found a bug\r\nIt needs more important features\r\nOther', 1, '', 'font-weight:bold', 80, 1, '2011-06-28 03:47:53', '2011-06-28 03:47:53', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(9, 1, 3, 'Comments', 'textarea', 'wide', '', 0, '', 'font-style:italic; color:#333', 90, 1, '2011-06-28 03:48:37', '2011-06-28 03:48:37', 1);
	INSERT INTO `{$this->getTable('webforms/fields')}` VALUES(10, 1, 2, 'Do you like this extension?', 'select/radio', 'standard', 'Yes\r\nYes, but it needs more features\r\nI`m not sure yet\r\nI don`t know how to use it\r\nNo, it lacks important features\r\nNo, it absolytely doesn`t suit my needs', 0, '', '', 65, 0, '2011-06-28 03:55:13', '2011-06-28 03:56:37', 1);

	INSERT INTO `{$this->getTable('webforms/fieldsets')}` VALUES(1, 1, 'Personal Info', 10, '2011-06-27 09:54:43', '2011-06-27 09:54:43', 1);
	INSERT INTO `{$this->getTable('webforms/fieldsets')}` VALUES(2, 1, 'Survey', 20, '2011-06-27 09:55:08', '2011-06-27 09:55:22', 1);
	INSERT INTO `{$this->getTable('webforms/fieldsets')}` VALUES(3, 1, 'Message', 30, '2011-06-27 09:55:48', '2011-06-27 09:55:48', 1);
	");
	
} else {
	
	$webform = Mage::getModel('webforms/webforms');
	$webform->setData(array(
		'name' => 'Contact Us',
		'description' => "<p>You can use this extension to create and manage web-forms on your sites.</p>",
		'success_text' => "<p>Thank you for contacting me.</p>\r\n<p>You should get notification e-mail to the address you specified in the form.</p>\r\n<p>If you wonder how survey results are proccessed. Its exported to Excel XML file from administration panel, then analyzed in Excel application.</p>",
		'registered_only' => 0,
		'send_email' => 1,
		'approve' => 0,
		'survey' => 0,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();

	$fieldset = Mage::getModel('webforms/fieldsets')->setData(array(
		'webform_id' => $webform->getId(),
		'name' => 'Personal Info',
		'position' => 10,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();

	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => '{{firstname}}',
		'name' => 'First name',
		'type' => 'text',
		'size' => 'standard',
		'position' => 10,
		'required' => 1,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => '{{lastname}}',
		'name' => 'Last name',
		'type' => 'text',
		'size' => 'standard',
		'position' => 20,
		'required' => 1,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => '{{email}}',
		'name' => 'E-mail address',
		'type' => 'email',
		'size' => 'wide',
		'position' => 30,
		'required' => 1,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	
	$fieldset = Mage::getModel('webforms/fieldsets')->setData(array(
		'webform_id' => $webform->getId(),
		'name' => 'Survey',
		'position' => 20,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => "Yes\r\nNo",
		'name' => 'Do you need custom web-forms on your site?',
		'type' => 'select/radio',
		'size' => 'standard',
		'position' => 40,
		'required' => 0,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => "Yes\r\nNo",
		'name' => 'Did you try other web-forms extensions?',
		'type' => 'select/radio',
		'size' => 'standard',
		'position' => 50,
		'required' => 0,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => '',
		'name' => 'What other web-forms extensions have you tried?',
		'type' => 'text',
		'size' => 'wide',
		'position' => 60,
		'required' => 0,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => "Yes, but it needs more features\r\nI`m not sure yet\r\nI don`t know how to use it\r\nNo, it lacks important features\r\nNo, it absolytely doesn`t suit my needs",
		'name' => 'Do you like this extension?',
		'type' => 'select/radio',
		'size' => 'standard',
		'position' => 65,
		'required' => 0,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => "Search engine\r\nMagento connect\r\nForums\r\nBlogs\r\nFriends\r\nLink from the other site",
		'name' => 'How did you find this page?',
		'type' => 'select/checkbox',
		'size' => 'standard',
		'position' => 70,
		'required' => 1,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();

	$fieldset = Mage::getModel('webforms/fieldsets')->setData(array(
		'webform_id' => $webform->getId(),
		'name' => 'Message',
		'position' => 30,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => "I like your extension\r\nI found a bug\r\nIt needs more important features\r\nOther",
		'name' => 'Subject',
		'type' => 'select',
		'size' => 'wide',
		'css_style' => 'font-weight:bold',
		'position' => 80,
		'required' => 1,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();
	
	$field = Mage::getModel('webforms/fields')->setData(array(
		'webform_id' => $webform->getId(),
		'fieldset_id' => $fieldset->getId(),
		'value' => '',
		'name' => 'Message',
		'type' => 'textarea',
		'size' => 'wide',
		'css_style' => 'font-style:italic; color:#333',
		'position' => 90,
		'required' => 1,
		'is_active' => 1,
		'created_time' => Mage::getSingleton('core/date')->gmtDate(),
		'update_time' => Mage::getSingleton('core/date')->gmtDate(),
	))->save();

}

$installer->endSetup();
?>