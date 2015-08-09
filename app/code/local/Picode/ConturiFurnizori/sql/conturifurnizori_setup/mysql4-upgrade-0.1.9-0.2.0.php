<?php

$installer = $this;
$installer->startSetup();

/**
 * add notes to some customer attributes
 */

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_profil');
$attribute->setData('note', 'Afisare profil furnizor in liste (categorie si subcategorii) si in rezultatele cautarilor.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_preferentiala');
$attribute->setData('note', 'Conturile PLATITE spre deosebire de cele GRATUITE beneficieaza de prioritate la afisarea in liste. Prin urmare, furnizorii cu conturi platite vor fi listati primii, in ordinea creditelor acumulate de la clienti.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_oferte');
$attribute->setData('note', 'Afisare oferte foto si/sau video in liste (categorii si subcategorii) si in rezultatele cautarilor.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_max_oferte_active');
$attribute->setData('note', 'Numarul maxim al ofertelor care pot fi active in acelasi timp.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_eticheta_oferta_speciala');
$attribute->setData('note', 'Posibilitatea de a seta una sau mai multe oferte ca "speciale". Aceste oferte sunt evidentiate fata de restul ofertelor prin aplicarea unor etichete cu textul "Oferta" sau "Promotie".')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_link_restul_ofertelor');
$attribute->setData('note', 'In functie de tipul contului, un furnizor poate avea una sau mai multe oferte active. Aceasta optiune daca este disponibila permite afisarea pe pagina de detalii al ofertei link-uri catre ofertele aceluias furnizor.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_link_alte_oferte');
$attribute->setData('note', 'Daca aceasta optiune este activa, pe pagina de detalii al ofertei vor fi prezentate link-uri catre oferte similare ale altor furnizori.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_album_prezentare');
$attribute->setData('note', 'Aceasta optiune ofera posibilitatea de a salva si prezenta clientilor unul sau mai multe albume de prezentare (portofolii foto).')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_max_album_active');
$attribute->setData('note', 'Numarul maxim al albumelor de prezentare care pot fi active in acelasi timp.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_spatiu_disc');
$attribute->setData('note', 'Spatiul in MB alocat pe server albumelor de prezentare.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_video_prezentare');
$attribute->setData('note', 'Aceasta optiune ofera posibilitatea prezentarii clientilor unui sau mai multor clipuri video de prezentare (portofolii video). Surse acceptate: Youtube.com si Vimeo.com')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_max_video_active');
$attribute->setData('note', 'Numarul maxim al clipurilor video care pot fi active in acelasi timp.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_notificari_sms');
$attribute->setData('note', 'Notificari prin SMS despre evenemintele petrecute pe website legate contului de furnizor.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_notificari_email');
$attribute->setData('note', 'Notificari prin email despre evenemintele petrecute pe website legate contului de furnizor.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_link_direct_website');
$attribute->setData('note', 'Afisarea link activ (direct) catre website-ul furnizorului.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_afisare_retele');
$attribute->setData('note', 'Afisare link activ (direct) catre retelele de socializare ale furnizorului.')->save();

$attribute   = Mage::getSingleton('eav/config')->getAttribute('customer', 'ac_op_rapoarte_avansate');
$attribute->setData('note', 'Listare rapoarte avansate despre evenimentele petrecute pe website legate contului de furnizor (vizite, afisari, comparatii, statistici si multe altele).')->save();

$installer->endSetup();
