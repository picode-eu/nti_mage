<?php
$installer = $this;
$installer->startSetup();

try{

$installer->run("
    CREATE TABLE IF NOT EXISTS `nti_usermessage` (
      `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `sender_id` int(11) unsigned DEFAULT NULL,
      `recever_id` int(11) unsigned NOT NULL,
      `sender_firstname` varchar(255) DEFAULT NULL,
      `sender_lastname` varchar(255) DEFAULT NULL,
      `subject` text NOT NULL,
      `message` text NOT NULL,
      `replay_to` varchar(100) NOT NULL,
      `is_first` int(1) unsigned NOT NULL DEFAULT '1',
      `first_message_id` int(11) DEFAULT NULL,
      `is_read` int(1) unsigned NOT NULL DEFAULT '0',
      `is_deleted` int(1) unsigned NOT NULL DEFAULT '0',
      `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`message_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");

}catch(Exception $e){
    
}

$installer->endSetup();