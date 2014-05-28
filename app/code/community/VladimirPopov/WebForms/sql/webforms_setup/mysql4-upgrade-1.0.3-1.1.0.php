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
ALTER TABLE  `$webforms_table` ADD  `approve` TINYINT( 1 ) NOT NULL AFTER `survey`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `result_label` VARCHAR( 255 ) NOT NULL AFTER  `name`;
ALTER TABLE  `{$this->getTable('webforms/results')}` ADD  `approved` TINYINT( 1 ) NOT NULL;
ALTER TABLE  `$webforms_table` ADD  `code` VARCHAR( 255 ) NOT NULL AFTER `name`;
ALTER TABLE  `$webforms_table` ADD  `redirect_url` TEXT NOT NULL AFTER `code`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `code` VARCHAR( 255 ) NOT NULL AFTER  `result_label`;
");

$installer->endSetup();
?>