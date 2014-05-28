<?php
/**
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2014 Vladimir Popov
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$webforms_table = 'webforms';

$edition = 'CE';
$version = explode('.', Mage::getVersion());
if ($version[1] >= 9)
	$edition = 'EE';

if((float)substr(Mage::getVersion(),0,3)>1.1 || $edition == 'EE')
	$webforms_table = $this->getTable('webforms/webforms');

$installer->run("
ALTER TABLE  `$webforms_table` ADD  `images_upload_limit` int( 11 ) NOT NULL DEFAULT '0' AFTER `approve`;
ALTER TABLE  `$webforms_table` ADD  `files_upload_limit` int( 11 ) NOT NULL DEFAULT '0' AFTER `approve`;
ALTER TABLE  `$webforms_table` ADD  `captcha_mode` varchar( 40 ) NOT NULL AFTER `approve`;
");

$installer->endSetup();
?>