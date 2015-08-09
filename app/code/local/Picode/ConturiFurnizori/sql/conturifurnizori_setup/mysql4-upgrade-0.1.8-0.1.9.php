<?php

$installer = $this;
$installer->startSetup();

/*
 * create new plan attributes
*/

$source = Mage::getModel('conturifurnizori/attribute_source_level');

$installer->addAttribute('catalog_product', 'cont_level', array
        (
            'group' => 'Detalii Cont',
            'label' => 'Nivel',
            'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
            'type' => 'varchar',
            'input' => 'select',
            'source' => 'conturifurnizori/attribute_source_level',
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
            'sort_order' => 2,
            'apply_to' => 'conturifurnizori',
        ) );


$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'cont_level');
$attribute->save();

/*********************************************************************************************************/

/**
 * reorder some furninor attributes
 */

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_status');
$attribute->setData('sort_order', 1)->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_type');
$attribute->setData('sort_order', 3)->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_expiration');
$attribute->setData('sort_order', 7)->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_trial_exp');
$attribute->setData('sort_order', 11)->save();

/*********************************************************************************************************/

/*
 * create new furnizor attributes
**/

$installer->addAttribute('customer', 'furnizor_account_online_status',  array(
    'type'     => 'varchar',
    'label'    => 'Online Status',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_online',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_online_status');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 4); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'furnizor_account_level',  array(
    'type'     => 'varchar',
    'label'    => 'Account Level',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_level',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_level');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 5); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'furnizor_account_trial_level',  array(
    'type'     => 'varchar',
    'label'    => 'Trial Level',
    'input'    => 'select',
    'source'   => 'conturifurnizori/attribute_source_level',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_account_trial_level');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 9); 
    
$attribute->save();

/*********************************************************************************************************/

/**
 * creat new account options
 */

$installer->addAttribute('customer', 'ac_op_afisare_profil',  array(
    'type'     => 'int',
    'label'    => 'Afisare profil furnizor',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_profil');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 100); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_afisare_preferentiala',  array(
    'type'     => 'int',
    'label'    => 'Afisare preferentiala in liste',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_preferentiala');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 105); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_afisare_oferte',  array(
    'type'     => 'int',
    'label'    => 'Afisare oferte (produse)',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_oferte');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 110); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_max_oferte_active',  array(
    'type'     => 'varchar',
    'label'    => 'Oferte (produse) active',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_max_oferte_active');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 115); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_eticheta_oferta_speciala',  array(
    'type'     => 'int',
    'label'    => 'Etichetare oferte speciala',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_eticheta_oferta_speciala');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 120); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_link_restul_ofertelor',  array(
    'type'     => 'int',
    'label'    => 'Link catre restul ofertelor',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_link_restul_ofertelor');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 125); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_link_alte_oferte',  array(
    'type'     => 'int',
    'label'    => 'Link catre oferte similare',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_link_alte_oferte');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 130); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_afisare_album_prezentare',  array(
    'type'     => 'int',
    'label'    => 'Afisare albume prezentare',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_album_prezentare');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 135); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_max_album_active',  array(
    'type'     => 'varchar',
    'label'    => 'Albume active',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_max_album_active');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 140); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_spatiu_disc',  array(
    'type'     => 'varchar',
    'label'    => 'Spatiu alocat pe disc',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_spatiu_disc');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 145); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_afisare_video_prezentare',  array(
    'type'     => 'int',
    'label'    => 'Afisare video clipuri prezentare',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_video_prezentare');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 150); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_max_video_active',  array(
    'type'     => 'varchar',
    'label'    => 'Videoclipuri active',
    'input'    => 'text',
    'source'   => '',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '',
    'class'    => '',
    'backend'  => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_max_video_active');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 155); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_notificari_sms',  array(
    'type'     => 'int',
    'label'    => 'Notificari instante prin SMS',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_notificari_sms');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 160); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_notificari_email',  array(
    'type'     => 'int',
    'label'    => 'Notificari instante prin email',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_notificari_email');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 165); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_link_direct_website',  array(
    'type'     => 'int',
    'label'    => 'Link direct catre website',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_link_direct_website');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 170); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_afisare_retele',  array(
    'type'     => 'int',
    'label'    => 'Afisare retele socializare',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_retele');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 175); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->addAttribute('customer', 'ac_op_rapoarte_avansate',  array(
    'type'     => 'int',
    'label'    => 'Rapoarte avansate',
    'input'    => 'boolean',
    'source'   => 'eav/entity_attribute_source_table',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'default'  => '0',
    'class'    => '',
    'backend' => '',
    'frontend' => '',
    'unique'   => false,
    'note'     => ''
    ));

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_rapoarte_avansate');
        
$used_in_forms = array();

$used_in_forms[] = 'adminhtml_customer';
$used_in_forms[] = 'checkout_register';
$used_in_forms[] = 'customer_account_create';
$used_in_forms[] = 'customer_account_edit';
$used_in_forms[] = 'adminhtml_checkout';

$attribute->setData('used_in_forms', $used_in_forms)
    ->setData('is_used_for_customer_segment', true)
    ->setData('is_system', false)
    ->setData('is_user_defined', true)
    ->setData('is_visible', true)
    ->setData('sort_order', 180); 
    
$attribute->save();

/*********************************************************************************************************/

$installer->endSetup();