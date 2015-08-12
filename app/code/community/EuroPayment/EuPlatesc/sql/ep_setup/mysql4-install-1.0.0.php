<?php
/**
 * EuPlatesc
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * available at http://opensource.org/licenses/gpl-3.0.html
 *
 * @category   EuPlatesc
 * @package    EuroPayment_EuPlatesc
 * @copyright  Copyright (c) 2008 EuroPayment Services - http://www.euplatesc.ro 
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author     euplatesc.ro - 2009
 */
$installer = $this;
/* @var $installer EuroPayment_EuPlatesc_Model_Mysql4_Setup */

$installer->startSetup();

$installer->run("

CREATE TABLE euplatesc_map (
  mage int(10) unsigned NOT NULL,
  epsro int(10) unsigned NOT NULL,
  KEY mage (mage,epsro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Map id of EuPlatesc.ro with Id of MAGE'

");

$installer->endSetup();
