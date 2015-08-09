<?php
$installer = $this;
$installer->startSetup();

$vCustomerEntityType = $installer->getEntityTypeId('customer');
$vCustAttributeSetId = $installer->getDefaultAttributeSetId($vCustomerEntityType);
$vCustAttributeGroupId = $installer->getDefaultAttributeGroupId($vCustomerEntityType, $vCustAttributeSetId);

$installer->addAttribute('customer', 'avatar', array(
    'type'  => 'varchar',
    'label' => 'Imagine de profil',
    'input' => 'file',
    'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'visible'  => true,
    'required' => false,
    'user_defined' => true,
    'note'     => '',
));

$installer->addAttributeToGroup(
					$vCustomerEntityType,
					$vCustAttributeSetId,
					$vCustAttributeGroupId,
					'avatar',
					0
				);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'avatar');
$oAttribute->setData('used_in_forms',
							array(
							  'adminhtml_customer',
							  'checkout_register',
							  'customer_account_edit',
							  'customer_account_create',
							  'adminhtml_checkout',
						    )
				     )
		    ->setData('is_used_for_customer_segment', true)
		    ->setData('is_system', false)
		    ->setData('is_user_defined', true)
		    ->setData('is_visible', true)
		    ->setData('sort_order', 1);
			
$oAttribute->save();

$installer->endSetup();