<?php

$installer = $this;

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$updateAttributes = array(
					  'ofg_customer_frontname' => '',
					  'ofg_customer_id' => '',
					  'ofg_tip_oferta' => '',
					  'ofg_valabilitate_zona' => '',
					  'ofg_zona_personalizata' => 'Valabila pentru:',
					  'ofg_cheltuieli_transport' => '',
					  'ofg_cheltuieli_cazare' => '',
					  'ofg_nr_fotografi' => '',
					  'ofg_nr_cameramani' => '',
					  'ofg_sedinta_logodna' => 'Sedinta logodna (inainte de nunta)',
					  'ofg_pregatiri_nunta' => '',
					  'ofg_cununie_civila' => '',
					  'ofg_cununie_civila_alta_zi' => '',
					  'ofg_nr_sedinte' => 'Nr. maxim sedinte programate',
					  'ofg_sedinte_inainte_biserica' => '',
					  'ofg_sedinte_dupa_biserica' => '',
					  'ofg_sedinte_suplimentara' => 'Alte sedinte foto/video (separate prin virgula)',
					  'ofg_cununie_religioasa' => '',
					  'ofg_restaurant' => '',
					  'ofg_restaurant_panala' => 'Disponibilitate la restaurant',
					  'ofg_restaurant_definit' => '',
					  'ofg_disponibilitate' => 'Timp alocat evenimentului',
					  'ofg_trash_the_dress' => 'Sedinta "Trash the Dress" (dupa nunta)',
					  'off_dvd' => 'DVD cu fotografii',
					  'off_dvd_nrcopii' => '',
					  'off_dvd_nr_img' => 'Nr. fotografii (minim)',
					  'off_slide_show' => '',
					  'off_slide_show_detalii' => '',
					  'off_album_clasic' => '',
					  'off_album_clasic_cant' => 'Nr. albume Foto Clasice',
					  'off_album_clasic_detalii' => '',
					  'off_album_carte' => 'Album FotoCarte',
					  'off_album_carte_cant' => 'Nr. albume FotoCarte',
					  'off_album_carte_detalii' => 'Descriere Album FotoCarte',
					  'ofv_format_filmare' => '',
					  'ofv_format_definit' => 'Alt format filmare (defineste)',
					  'ofv_montaj_dvd' => 'Filmarea livrata pe DVD',
					  'ofv_montaj_dvd_nrcopii' => '',
					  'ofv_montaj_blu_ray' => 'Filmarea livrata pe Blu-Ray',
					  'ofv_montaj_blu_ray_nrcopii' => '',
					  'ofv_montaj_durata' => 'Durata montaj video (filmul nuntii)',
					  'ofv_videoclip' => 'Videoclip intro (rezumat)',
					  'ofv_videoclip_durata' => 'Durata videoclip intro (minute)',
					  'ofv_detalii_suplimentare' => 'Detalii suplimentare',
				   );
				   
foreach ($updateAttributes as $code => $value) {
	$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);
	
	if ($value != ''){
		$attribute->setFrontendLabel($value);
	} 
	
	$attribute->setIsRequired('0');
	$attribute->save();
}		

/*****************************************************************************************/		   

$installer->addAttribute('catalog_product', 'ofv_montaj_film', array
        (
            'group' => 'Oferta Video',
            'label' => 'Montaj video (filmul nuntii)',
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
            'searchable' => true,
            'filterable' => true,
            'comparable' => true,
            'visible_on_front' => true,
            'visible_in_advanced_search' => true,
            'used_in_product_listing' => true,
            'unique' => false,
            'sort_order' => 33,
            'apply_to' => 'ofertefurnizori',
        ) );

$attribute   = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'ofv_montaj_film');
$attribute->save();

/********************************************************************************************/


$installer->endSetup();