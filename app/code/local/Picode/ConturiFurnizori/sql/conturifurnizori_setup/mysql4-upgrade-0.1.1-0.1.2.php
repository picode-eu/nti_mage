<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'billing_cui',
    'varchar(255) NULL DEFAULT NULL AFTER `fax`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'billing_nrc',
    'varchar(255) NULL DEFAULT NULL AFTER `billing_cui`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'billing_banca',
    'varchar(255) NULL DEFAULT NULL AFTER `billing_nrc`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'billing_iban',
    'varchar(255) NULL DEFAULT NULL AFTER `billing_banca`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'billing_ci',
    'varchar(255) NULL DEFAULT NULL AFTER `billing_iban`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'billing_cnp',
    'varchar(255) NULL DEFAULT NULL AFTER `billing_ci`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote_address'),
    'street_other',
    'varchar(255) NULL DEFAULT NULL AFTER `street`'
);

$installer->endSetup();