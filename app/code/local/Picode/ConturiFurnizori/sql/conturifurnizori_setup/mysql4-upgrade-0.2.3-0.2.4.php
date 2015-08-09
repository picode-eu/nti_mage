<?php

$installer = $this;

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

/**
 * start adding the attributes of Oferta Video
 */

$installer->addAttribute('catalog_product', 'off_dvd', array
        (
            'group' => 'Oferta Foto',
            'label' => 'DVD Foto',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 5,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_dvd');
$attribute->save();

/********************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_numbers');

$installer->addAttribute('catalog_product', 'off_dvd_nrcopii', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Nr. copii DVD (seturi)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_numbers',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 10,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_dvd_nrcopii');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'off_dvd_nr_img', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Nr imagini de DVD (minim)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'text',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 15,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_dvd_nr_img');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'off_slide_show', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Slide Show foto',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 20,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_slide_show');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'off_slide_show_detalii', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Detalii Slide Show (descriere)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'text',
            'input' => 'textarea',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 25,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_slide_show_detalii');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'off_album_clasic', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Album Foto clasic',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 30,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_album_clasic');
$attribute->save();

/********************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_numbers');

$installer->addAttribute('catalog_product', 'off_album_clasic_cant', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Nr. Album(e) Foto clasic',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_numbers',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 35,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_album_clasic_cant');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'off_album_clasic_detalii', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Detalii Album Foto (descriere)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'text',
            'input' => 'textarea',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 40,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_album_clasic_detalii');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'off_album_carte', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Album tip foto-carte (digital)',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 45,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_album_carte');
$attribute->save();

/********************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_numbers');

$installer->addAttribute('catalog_product', 'off_album_carte_cant', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Nr. Album(e) foto-carte',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_numbers',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 50,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_album_carte_cant');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'off_album_carte_detalii', array
        (
            'group' => 'Oferta Foto',
            'label' => 'Detalii Album foto-carte (descriere)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'text',
            'input' => 'textarea',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 55,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'off_album_carte_detalii');
$attribute->save();

/********************************************************************************************/

/**
 * start adding the attributes of Oferta Video
 */

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_formatvideo');

$installer->addAttribute('catalog_product', 'ofv_format_filmare', array
        (
            'group' => 'Oferta Video',
            'label' => 'Format filmare',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_formatvideo',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 5,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_format_filmare');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'ofv_format_definit', array
        (
            'group' => 'Oferta Video',
            'label' => 'Alt format video (defineste)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'text',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 10,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_format_definit');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'ofv_montaj_dvd', array
        (
            'group' => 'Oferta Video',
            'label' => 'Montaj DVD',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 15,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_montaj_dvd');
$attribute->save();

/********************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_numbers');

$installer->addAttribute('catalog_product', 'ofv_montaj_dvd_nrcopii', array
        (
            'group' => 'Oferta Video',
            'label' => 'Nr. copii DVD (seturi)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_numbers',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 20,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_montaj_dvd_nrcopii');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'ofv_montaj_blu_ray', array
        (
            'group' => 'Oferta Video',
            'label' => 'Montaj Blu-ray',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 25,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_montaj_blu_ray');
$attribute->save();

/********************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_numbers');

$installer->addAttribute('catalog_product', 'ofv_montaj_blu_ray_nrcopii', array
        (
            'group' => 'Oferta Video',
            'label' => 'Nr. copii Blu-ray (seturi)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_numbers',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 30,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_montaj_blu_ray_nrcopii');
$attribute->save();

/********************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_duratamontaj');

$installer->addAttribute('catalog_product', 'ofv_montaj_durata', array
        (
            'group' => 'Oferta Video',
            'label' => 'Durata montaj video',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_duratamontaj',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 35,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_montaj_durata');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'ofv_videoclip', array
        (
            'group' => 'Oferta Video',
            'label' => 'Videoclip Intro (rezumat nunta)',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 40,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_videoclip');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'ofv_videoclip_durata', array
        (
            'group' => 'Oferta Video',
            'label' => 'Durata videoclip (minute)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'text',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 45,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_videoclip_durata');
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('catalog_product', 'ofv_detalii_suplimentare', array
        (
            'group' => 'Oferta Video',
            'label' => 'Detalii suplimentare filmare',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'text',
            'input' => 'textarea',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 50,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_detalii_suplimentare');
$attribute->save();

/********************************************************************************************/

$installer->endSetup();