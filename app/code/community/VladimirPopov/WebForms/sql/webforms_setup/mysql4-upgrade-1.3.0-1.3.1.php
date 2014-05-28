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
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_length_min` int( 11 ) NOT NULL DEFAULT '0' AFTER `css_style`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_length_max` int( 11 ) NOT NULL DEFAULT '0' AFTER `css_style`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_regex` varchar( 255 ) NOT NULL AFTER `css_style`;
ALTER TABLE  `{$this->getTable('webforms/fields')}` ADD  `validate_message` text NOT NULL AFTER `css_style`;
");

$installer->endSetup();
?>