<?php

$installer = $this;

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

/**
 * add a new product attribute set
 * Name: Oferte Furnizori
 * Clone set: Default (id 4)
*/

$model = Mage::getModel('eav/entity_attribute_set');
$newAttributeSetName = 'Oferte Furnizori';
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

// set new attribute set if not exists
if (!$setExists) {
    $attributeSet = $model->setEntityTypeId($entityTypeId)->setAttributeSetName($newAttributeSetName);
    $attributeSet->validate();
    $attributeSet->save();
    $attributeSet->initFromSkeleton($cloneSetId)->save();
}

/*
 * creates two new attribute groups
 * Name: Oferta Foto, Sort order 2
 * Name: Oferta Video, Sort order 3
 * S-ar putea sa fie nevoie sa trebuiasca rearanjate pozitia taburilor din adim
 */
$installer->addAttributeGroup('catalog_product', $newAttributeSetName, 'Detalii Generale', 2);
$installer->addAttributeGroup('catalog_product', $newAttributeSetName, 'Oferta Foto', 3);
$installer->addAttributeGroup('catalog_product', $newAttributeSetName, 'Oferta Video', 4);

/*
 * create new oferta attributes
*/

$installer->addAttribute('catalog_product', 'ofg_customer_frontname', array
        (
            'group' => 'Detalii Generale',
            'label' => 'Nume furnizor',
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


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_customer_frontname');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_customer_id', array
        (
            'group' => 'Detalii generale',
            'label' => 'Customer ID',
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
            'sort_order' => 10,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_customer_id');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_tipoferta');

$installer->addAttribute('catalog_product', 'ofg_tip_oferta', array
        (
            'group' => 'Detalii generale',
            'label' => 'Tip oferta',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_services',
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
            'sort_order' => 15,
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_tip_oferta');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_zone');

$installer->addAttribute('catalog_product', 'ofg_valabilitate_zona', array
        (
            'group' => 'Detalii generale',
            'label' => 'Valabilitate',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_zone',
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
            'sort_order' => 20,
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_valabilitate_zona');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_zona_personalizata', array
        (
            'group' => 'Detalii Generale',
            'label' => 'Selecteaza judetele',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 25,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_zona_personalizata');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_cheltuieli');

$installer->addAttribute('catalog_product', 'ofg_cheltuieli_transport', array
        (
            'group' => 'Detalii generale',
            'label' => 'Cheltuieli transport',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_cheltuieli',
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
            'sort_order' => 30,
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_cheltuieli_transport');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_cheltuieli');

$installer->addAttribute('catalog_product', 'ofg_cheltuieli_cazare', array
        (
            'group' => 'Detalii generale',
            'label' => 'Cheltuieli cazare (daca este cazul) ',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_cheltuieli',
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
            'sort_order' => 35,
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_cheltuieli_cazare');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_numbers');

$installer->addAttribute('catalog_product', 'ofg_nr_fotografi', array
        (
            'group' => 'Detalii generale',
            'label' => 'Nr. fotografi',
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
            'sort_order' => 40,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_nr_fotografi');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_numbers');

$installer->addAttribute('catalog_product', 'ofg_nr_cameramani', array
        (
            'group' => 'Detalii generale',
            'label' => 'Nr. cameramani',
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
            'sort_order' => 45,
            'note' => '',
            'apply_to' => 'ofertefurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_nr_cameramani');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_sedinta_logodna', array
        (
            'group' => 'Detalii generale',
            'label' => 'Sedinta logodna',
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
            'sort_order' => 50,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_sedinta_logodna');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_pregatiri_nunta', array
        (
            'group' => 'Detalii generale',
            'label' => 'Pregatiri nunta',
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
            'sort_order' => 55,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_pregatiri_nunta');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_cununie_civila', array
        (
            'group' => 'Detalii generale',
            'label' => 'Cununie civila',
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
            'sort_order' => 60,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_cununie_civila');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_cununie_civila_alta_zi', array
        (
            'group' => 'Detalii generale',
            'label' => 'Cununie civila in alta zi',
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
            'sort_order' => 65,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_cununie_civila_alta_zi');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_nrsedinte');

$installer->addAttribute('catalog_product', 'ofg_nr_sedinte', array
        (
            'group' => 'Detalii generale',
            'label' => 'Nr. sedinte programate',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_nrsedinte',
            'backend' => 'eav/entity_attribute_backend_array',
            'option' => $source->getOptionArray(),
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
            'sort_order' => 70,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_nr_sedinte');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_sedinte_inainte_biserica', array
        (
            'group' => 'Detalii generale',
            'label' => 'Sedinta inainte de biserica',
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
            'sort_order' => 75,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_sedinte_inainte_biserica');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_sedinte_dupa_biserica', array
        (
            'group' => 'Detalii generale',
            'label' => 'Sedinta dupa de biserica',
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
            'sort_order' => 80,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_sedinte_dupa_biserica');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_sedinte_suplimentara', array
        (
            'group' => 'Detalii generale',
            'label' => 'Sedinta suplimentara (defineste)',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 85,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_sedinte_suplimentara');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_cununie_religioasa', array
        (
            'group' => 'Detalii generale',
            'label' => 'Cununie religioasa',
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
            'sort_order' => 90,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_cununie_religioasa');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_restaurant', array
        (
            'group' => 'Detalii generale',
            'label' => 'Restaurant',
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
            'sort_order' => 95,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_restaurant');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_panala');

$installer->addAttribute('catalog_product', 'ofg_restaurant_panala', array
        (
            'group' => 'Detalii generale',
            'label' => 'Pana la',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_panala',
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
            'sort_order' => 100,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_restaurant_panala');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_restaurant_definit', array
        (
            'group' => 'Detalii generale',
            'label' => 'Alt moment (defineste)',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 105,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_restaurant_definit');
$attribute->save();

/***************************************************************************************************/

$source = Mage::getModel('conturifurnizori/attribute_source_oferta_disponibilitate');

$installer->addAttribute('catalog_product', 'ofg_disponibilitate', array
        (
            'group' => 'Detalii generale',
            'label' => 'Disponibilitatea (echipei)',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_oferta_disponibilitate',
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
            'sort_order' => 110,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_disponibilitate');
$attribute->save();

/***************************************************************************************************/

$installer->addAttribute('catalog_product', 'ofg_trash_the_dress', array
        (
            'group' => 'Detalii generale',
            'label' => 'Sedinta "Trash the Dress"',
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
            'sort_order' => 115,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofg_trash_the_dress');
$attribute->save();

/***************************************************************************************************/

$installer->endSetup();














