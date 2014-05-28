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
ALTER TABLE  `{$webforms_table}` ADD  `submit_button_text` varchar( 255 ) ;
");

$installer->endSetup();
