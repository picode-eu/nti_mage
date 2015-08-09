<?php
$installer = $this;$installer->startSetup();
$installer->getConnection()->addColumn(    $installer->getTable('sales_flat_quote'),    'business_descriptions_title',    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_company_name`');
$installer->getConnection()->addColumn(    $installer->getTable('sales_flat_quote'),    'furnizor_location_address',    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_location_province`');
$installer->getConnection()->addColumn(    $installer->getTable('sales_flat_quote'),    'furnizor_location_number',    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_location_address`');
$installer->endSetup();