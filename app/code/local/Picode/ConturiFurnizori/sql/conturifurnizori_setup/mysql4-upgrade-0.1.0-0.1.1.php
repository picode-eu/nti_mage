<?php

$installer = $this;

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

/**
 * add a new product attribute set
 * Name: Conturi
 * Clone set: Default (id 4)
*/

$model = Mage::getModel('eav/entity_attribute_set');
$newAttributeSetName = 'Conturi Furnizori';
$setExists = false;
$cloneSetId = 4; // 4 is for Default
$entityTypeId = Mage::getModel('catalog/product')->getResource()->getEntityType()->getId();
//product entity type

// check if the attribute set already exists or not
$attributeSetCollection = $model->setEntityTypeId($entityTypeId)->getCollection();

foreach ($attributeSetCollection as $set) {
    if ($set['attribute_set_name'] == $newAttributeSetName) {
        // if the attribute set was faund stop further checks
        $setExists = true;
        break;
    }
}

// add new attribute set if not exists
if (!$setExists) {
    $attributeSet = $model->setEntityTypeId($entityTypeId)->setAttributeSetName($newAttributeSetName);
    $attributeSet->validate();
    $attributeSet->save();
    $attributeSet->initFromSkeleton($cloneSetId)->save();
}

/*
 * creates a new attribute group
 * Name: Detalii Cont, Sort Order: 2
 */
$installer->addAttributeGroup('catalog_product', $newAttributeSetName, 'Detalii Cont', 1);

/*
 * create new plan attributes
*/

$source = Mage::getModel('conturifurnizori/attribute_source_tipcont');

$installer->addAttribute('catalog_product', 'cont_tip', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Tip Cont',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_tipcont',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 10,
            'apply_to' => 'conturifurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_tip');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_activeaza_testare', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Activeaza Testare',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 15,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_activeaza_testare');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_perioadatestare');

$installer->addAttribute('catalog_product', 'cont_perioada_testare', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Perioada Testare',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_perioadatestare',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 20,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_perioada_testare');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_testare_dela', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Testare activa de la:',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'datetime',
            'input' => 'date',
            'source' => '',
            'backend' => 'eav/entity_attribute_backend_datetime',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 25,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_testare_dela');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_testare_panala', array(
            'group' => 'Detalii Cont',
            'label' => 'Testare activa pana la:',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'datetime',
            'input' => 'date',
            'source' => '',
            'backend' => 'eav/entity_attribute_backend_datetime',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 30,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_testare_panala');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_afisare_preferentiala', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Afisare preferentiala in liste',
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
            'sort_order' => 35,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_afisare_preferentiala');
$attribute->save();

/***************************************************************************************************/



$installer->addAttribute('catalog_product', 'cont_max_oferte_active', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Oferte active',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'text',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 40,
            'note' => 'Ex. 3 (integer)',
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_max_oferte_active');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_link_restul_ofertelor', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Link catre restul ofertelor',
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
            'sort_order' => 45,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_link_restul_ofertelor');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_link_alte_oferte', array
(
            'group' => 'Detalii Cont',
            'label' => 'Link catre oferte similare',
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
            'sort_order' => 50,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_link_alte_oferte');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_max_album_active', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Albume active',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'text',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 55,
            'note' => 'Ex. 3 (integer)',
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_max_album_active');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_spatiu_disc', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Spatiu alocat pe disc',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'text',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 60,
            'note' => 'Spatiu pe disc in MB (integer), Ex. 25',
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_spatiu_disc');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_max_video_active', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Videoclipuri active',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'text',
            'source' => '',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => true,
            'user_defined' => true,
            'default' => '',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 65,
            'note' => 'Ex. 3 (integer)',
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_max_video_active');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_notificari_email', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Notificari instante prin email',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 70,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_notificari_email');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_notificari_sms', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Notificari instante prin SMS',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 75,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_notificari_sms');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_link_direct_website', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Link direct catre website',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 75,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_link_direct_website');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_afisare_retele', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Afisare retele socializare',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 75,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_afisare_retele');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'cont_rapoarte_avansate', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Rapoarte avansate',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'int',
            'input' => 'boolean',
            'source' => 'eav/entity_attribute_source_table',
            'backend' => '',
            'frontend' => '',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'default' => 0,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => true,
            'visible_in_advanced_search' => false,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 80,
            'apply_to' => 'conturifurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_rapoarte_avansate');
$attribute->save();

/***************************************************************************************************/

$installer->endSetup();

