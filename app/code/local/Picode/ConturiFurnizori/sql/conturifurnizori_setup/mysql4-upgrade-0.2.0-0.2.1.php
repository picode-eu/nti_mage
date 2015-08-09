<?php

$installer = $this;
$installer->startSetup();

/**
 * translate some customer attribut's label
 * add notes to some customer attributes
 */

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_name')
    ->setData('label', 'Denumire Firma / PFA')
    ->setData('note', 'Asa cum apare in acte (privat)')
    ->save();
    
$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_title')
    ->setData('label', 'Denumire comerciala')
    ->setData('note', 'Asa cum vrei sa apara in liste (public)')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_type')
    ->setData('label', 'Tip Organizare')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_company_services')
    ->setData('label', 'Servicii prestate')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_province')
    ->setData('label', 'Judet')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_city')
    ->setData('label', 'Localitate')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_address')
    ->setData('label', 'Strada, nr. si alte detalii')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_location_zip')
    ->setData('label', 'Cod postal')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_firstname')
    ->setData('label', 'Nume')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_lastname')
    ->setData('label', 'Prenume')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_email')
    ->setData('label', 'Email')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'furnizor_contact_phone')
    ->setData('label', 'Telefon')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_exp')
    ->setData('label', 'Experienta')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_desc')
    ->setData('label', 'Descriere activitate')
    //->setData('note', '')
    ->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_descriptions_aparat')
    ->setData('label', 'Descriere echipament')
    //->setData('note', '')
    ->save();
    
/*** didn't work, I don't nkow why ***/

/**********************************************************************************************************/

$installer->addAttribute('customer', 'business_networks_website',  array(
    'type'     => 'varchar',
    'label'    => 'Website / Blog',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_website');
        
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
    ->setData('sort_order', 40);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_webshortdesc',  array(
    'type'     => 'text',
    'label'    => 'Descriere website / blog',
    'input'    => 'textarea',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_webshortdesc');
        
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
    ->setData('sort_order', 45);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_skype',  array(
    'type'     => 'varchar',
    'label'    => 'ID Skype',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_skype');
        
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
    ->setData('sort_order', 50);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_messenger',  array(
    'type'     => 'varchar',
    'label'    => 'ID Yahoo Messenger',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_messenger');
        
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
    ->setData('sort_order', 55);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_facebook',  array(
    'type'     => 'varchar',
    'label'    => 'Facebook',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_facebook');
        
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
    ->setData('sort_order', 60);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_tweeter',  array(
    'type'     => 'varchar',
    'label'    => 'Tweeter',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_tweeter');
        
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
    ->setData('sort_order', 65);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_gplus',  array(
    'type'     => 'varchar',
    'label'    => 'Google+',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_gplus');
        
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
    ->setData('sort_order', 70);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_linkedin',  array(
    'type'     => 'varchar',
    'label'    => 'LinkedIn',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_linkedin');
        
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
    ->setData('sort_order', 75);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_youtube',  array(
    'type'     => 'varchar',
    'label'    => 'Youtube',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_youtube');
        
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
    ->setData('sort_order', 80);
    
$attribute->save();

/********************************************************************************************/

$installer->addAttribute('customer', 'business_networks_vimeo',  array(
    'type'     => 'varchar',
    'label'    => 'Vimeo',
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

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'business_networks_vimeo');
        
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
    ->setData('sort_order', 85);
    
$attribute->save();

/********************************************************************************************/

$installer->endSetup();