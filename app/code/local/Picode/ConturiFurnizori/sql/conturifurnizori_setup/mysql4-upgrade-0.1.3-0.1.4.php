<?php

$installer = $this;
$installer -> startSetup();

/**
 * add new catalog-product attributes
 */

$installer->addAttribute('catalog_product', 'cont_afisare_profil', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Afisare profil furnizor',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 33,
            'apply_to' => 'conturifurnizori',
        ) );

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_afisare_oferte', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Afisare oferte (produse)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 38,
            'apply_to' => 'conturifurnizori',
        ) );

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_eticheta_oferta_speciala', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Etichetare oferte speciala',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 42,
            'apply_to' => 'conturifurnizori',
        ) );

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_afisare_album_prezentare', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Afisare albume prezentare',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 53,
            'apply_to' => 'conturifurnizori',
        ) );

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_afisare_video_prezentare', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Afisare video clipuri prezentare',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 63,
            'apply_to' => 'conturifurnizori',
        ) );

/***************************************************************************************************/

$installer->endSetup();