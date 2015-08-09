<?php
$installer = $this;
$installer->startSetup();

$vCustomerEntityType = $installer->getEntityTypeId('customer');
$vCustAttributeSetId = $installer->getDefaultAttributeSetId($vCustomerEntityType);
$vCustAttributeGroupId = $installer->getDefaultAttributeGroupId($vCustomerEntityType, $vCustAttributeSetId);

$installer->addAttribute('customer', 'loved_offers', array(
    'type'  => 'varchar',
    'label' => 'Loved Offers',
    'input' => 'text',
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
	'loved_offers',
	0
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'loved_offers');
$oAttribute->setData('used_in_forms',
							array(
							  'adminhtml_customer',
							  // 'checkout_register',
							  'customer_account_edit',
							  // 'customer_account_create',
							  // 'adminhtml_checkout',
						    )
				     )
		    ->setData('is_used_for_customer_segment', true)
		    ->setData('is_system', false)
		    ->setData('is_user_defined', true)
		    ->setData('is_visible', true)
		    ->setData('sort_order', 2);
			
$oAttribute->save();

$installer->endSetup();