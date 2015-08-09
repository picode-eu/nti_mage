<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'business_images_logo',
    'varchar(255) NULL DEFAULT NULL AFTER `business_descriptions_exp`'
);

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_quote'),
    'business_descriptions_desc',
    'text NULL DEFAULT NULL AFTER `business_descriptions_exp`'
);

$installer->endSetup();