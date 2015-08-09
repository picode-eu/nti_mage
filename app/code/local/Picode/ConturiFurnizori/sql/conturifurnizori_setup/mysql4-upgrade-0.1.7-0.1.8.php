<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_company_name',
    'varchar(255) NULL DEFAULT NULL AFTER `customer_is_guest`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_company_type',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_company_name`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_company_services',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_company_type`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'business_descriptions_exp',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_company_services`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_contact_firstname',
    'varchar(255) NULL DEFAULT NULL AFTER `business_descriptions_exp`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_contact_lastname',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_contact_firstname`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_contact_email',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_contact_lastname`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_contact_phone',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_contact_email`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_location_city',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_contact_phone`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'furnizor_location_province',
    'varchar(255) NULL DEFAULT NULL AFTER `furnizor_location_city`'
);

$installer->endSetup();