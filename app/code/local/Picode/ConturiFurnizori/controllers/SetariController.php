<?php
class Picode_ConturiFurnizori_SetariController extends Mage_Core_Controller_Front_Action
{
    /**
     * Check if customer is logged in or not
     * If not logged in then redirect to customer login
     */
    public function preDispatch()
    {
        parent::preDispatch();
     
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
	
	/**
     * Define allawed register form inputs
     * 
     * @return array
     */
    private $_allowedInputs = array(
                'save',
				'form_key',
                // profil section
                'business_images_coperta',
                'business_images_logo',
				'furnizor_company_name',
				'business_descriptions_title',
				'furnizor_company_type',
				'furnizor_company_services',
				'furnizor_account_online_status',
				'furnizor_company_zone',
				'furnizor_company_cstzone',
				'business_descriptions_slogan',
				'business_descriptions_exp',
				'business_descriptions_desc',
				'business_descriptions_aparat',
				'furnizor_location_province',
				'furnizor_location_city',
				'furnizor_location_address',
				'furnizor_location_number',
				'furnizor_location_other',
				'furnizor_location_zip',
				'furnizor_contact_firstname',
				'furnizor_contact_lastname',
				'furnizor_contact_email',
				'furnizor_contact_phone',
				'business_networks_website',
				'business_networks_webshortdesc',
				'business_networks_facebook',
				'business_networks_tweeter',
				'business_networks_gplus',
				'business_networks_linkedin',
				'business_networks_youtube',
				'business_networks_vimeo',
				'business_networks_skype',
				'business_networks_messenger',
				// other section
            );
            
    /**
     * Get customer session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    protected function _checkFurnizoriGroup()
    {
        if ($this->_getSession()->getCustomer()->getGroupId() != '4') { // 4 is for furnizori
            $this->_redirect('customer/account/');
            return;
        }
        
        return;
    }
    
    public function optiuniAction()
    {
        /**
         * Check if customer is furnizor or not
         * If not logged in then redirect to customer account dashboard
         */
        $this->_checkFurnizoriGroup();
        
        /**
         * continue if customer is furnizor
         */
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Optiuni furnizor'));
        $this->renderLayout();
    }
    
    public function profilAction()
    {
        /**
         * Check if customer is furnizor or not
         * If not logged in then redirect to customer account dashboard
         */
        $this->_checkFurnizoriGroup();
        
        /**
         * continue if customer is furnizor
         */
        
        // start saving form's data if exists
        if ($params = $this->getRequest()->getParams())
        {
        	//Zend_Debug::dump($params); die();
        	$unrequiredFields = array(
				'business_images_coperta',
				'business_images_logo',
				'business_descriptions_title',
				'furnizor_company_cstzone',
				'business_descriptions_slogan',
				'business_descriptions_aparat',
				'furnizor_location_address',
				'furnizor_location_number',
				'furnizor_location_other',
				'furnizor_location_zip',
				'furnizor_contact_firstname',
				'furnizor_contact_lastname',
				'furnizor_contact_phone',
				'business_networks_website',
				'business_networks_webshortdesc',
				'business_networks_skype',
				'business_networks_messenger',
				'business_networks_facebook',
				'business_networks_tweeter',
				'business_networks_gplus',
				'business_networks_linkedin',
				'business_networks_youtube',
				'business_networks_vimeo'
			);
            
			$customer = $this->_getSession()->getCustomer();
            $tempUploadDir = Mage::getBaseDir('media') . DS . 'tmp_uploads' . DS . $customer->getId() . DS;
			// saving form's data
            foreach ($params as $name => $value) {
                if ($name != 'data' && $name != 'save' && $name != 'form_key') {
                	if (!in_array($name, $unrequiredFields) && $value == '') {
		                $errorMessage = 'Campurile marcate cu * sunt obligatorii.';
						Mage::getSingleton('core/session')->addError($errorMessage);
						$this->_redirect('conturifurnizori/setari/profil/');
						return;
		            } else {
		                if (in_array($name, $this->_allowedInputs)){
		                    //Zend_Debug::dump($params); die();
		                	if ($name == 'business_images_coperta' || $name == 'business_images_logo') {
		                		// move image to final location
		                		$nameArr = explode('_', $name);
								$newFileName = $this->removeSpecialCharacters($params['business_descriptions_title']). '-' . end($nameArr) . '-' . time();
                                
		                		if ($newValue = $this->moveUploadedFile($tempUploadDir, $value, $newFileName)) {
		                			$customer->setData($name, $newValue);
		                		}
                            
							} elseif ($name == 'furnizor_company_cstzone') {
								// "Zona de activitate" is "Personalizat"
								$zones = implode(',', $value);
								$customer->setData($name, $zones);
		                	} else {
		                		$customer->setData($name, $value);
		                	}
							
							// "Zona de activitate" is other than "Personalizat"
							if (!isset($zones)) {
								$customer->setData('furnizor_company_cstzone', '');
							}
		                    
		                } else {
		                	$errorMessage = 'Ceva nesateptat s-a intamlat. Verifica datele furnizate si incearca din nou.<br />Daca situatia persista nu ezita sa ne contactezi.';
                            Mage::log('error 1', false, 'saveProfile.log');
							Mage::getSingleton('core/session')->addError($errorMessage);
							$this->_redirect('conturifurnizori/setari/profil/');
							return;
		                }
		            }
                }
            }
            
			try {
                // check customer account status
                $accountStatus = $customer->getFurnizorAccountStatus();
                $accountStatusText = $customer->getResource()->getAttribute('furnizor_account_status')->getFrontend()->getValue($customer);
                
                switch ($accountStatus) {
                    case '2': // 2 si for "Finalizare plata"
                        $now = strtotime(date("Y-m-d 00:00:00",  strtotime(Mage::getModel('core/date')->date('Y-m-d H:i:s') . ' -1 hour')));
                        if ($now <= strtotime($customer->getFurnizorAccountTrialExp())){
                            Mage::getSingleton('core/session')->addNotice('Contul tău de furnizor este în stadiul de "<strong>' . $accountStatusText . '</strong>" iar termenul de plată al facturii este <strong>' . date('d.m.Y', strtotime($customer->getFurnizorAccountTrialExp())) . '</strong>. După expirarea acestui termen vizibilitatea profilul tău de furnizor va deveni <strong>privată</strong>. Dacă nu ai primit factura proformă te rugăm să ne contactezi pentru a-ți retrimite o copie sau alege să plătești online pentru activare imediată.');
                        } else {
                            $customer->setFurnizorAccountOnlineStatus(0);
                            Mage::getSingleton('core/session')->addNotice('Contul tău de furnizor este în stadiul de "<strong>' . $accountStatusText . '</strong>" iar termenul de plată al facturii a expirat în <strong>' . date('d.m.Y', strtotime($customer->getFurnizorAccountTrialExp())) . '</strong>. Profilul nu poate fi public decât pentru conturile active. Dacă nu ai primit factura te rugăm să ne contactezi pentru a-ți retrimite o copie.');
                        }
                        break;
                        
                    case '3': // 3 is for "Aprobare"
                        $customer->setFurnizorAccountOnlineStatus(0);
                        Mage::getSingleton('core/session')->addNotice('Contul tău de furnizor este în stadiul de "<strong>' . $accountStatusText . '</strong>". Profilul nu poate fi public decât pentru conturile active.');
                        break;
                        
                    case '4': // 4 is for "Blocat"
                        $customer->setFurnizorAccountOnlineStatus(0);
                        Mage::getSingleton('core/session')->addNotice('Contul tău de furnizor este în stadiul "<strong>' . $accountStatusText . '</strong>". Te rugăm să ne contactezi pentru a analiza situația.');
                        break;
                        
                    case '5': // 5 si for "Suspendat"
                        $customer->setFurnizorAccountOnlineStatus(0);
                        Mage::getSingleton('core/session')->addNotice('Contul tău a fost "<strong>' . $accountStatusText . '</strong>" ca urmare a inactivității.');
                        break;
                    default:
                        // set succes message and redirect
                        Mage::getSingleton('core/session')->addSuccess('Noile setări au fost salvate.');
                }
                
                $customer->save();
                // remove temp dir and all its files
                $this->removeNonEmptyDir($tempUploadDir);
                
				$this->_redirect('conturifurnizori/setari/profil/');
				return;
            }
            catch (Exception $ex) {
                Mage::log($ex->getMessage(), false, 'register_provider.log');
                // redirect to provider registration with error message
                Mage::getSingleton('core/session')->addError('Ceva nesateptat s-a intamlat la salvarea datelor. Verifica datele furnizate si incearca din nou.<br />Daca situatia persista nu ezita sa ne contactezi.');
                Mage::log('error 2', false, 'saveProfile.log');
                $this->_redirect('conturifurnizori/inregistrare/');
                return ;
            }
        }
        
        $this->loadLayout();
        
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        
        $this->getLayout()->getBlock('head')->setTitle($this->__('Profil furnizor'));
        $this->renderLayout();
    }

	public function moveUploadedFile($path, $file, $newFileName)
	{
		if (file_exists($path . $file)) {
			// define some initial data
			$fileArr = explode('.', $file);
			$ext = end($fileArr);
			$customer = $this->_getSession()->getCuistomer();
			// create final directori structure
			$finalDirectory = Mage::getBaseDir('media') . DS . 'customer' . DS;
			if (!is_dir($finalDirectory)) {
				mkdir($finalDirectory, 0777);
			}
			// continue
			$finalDirectory .= $newFileName[0] . DS;
			if (!is_dir($finalDirectory)) {
				mkdir($finalDirectory, 0777);
			}
			// continue
			$finalDirectory .= $newFileName[1] . DS;
			if (!is_dir($finalDirectory)) {
				mkdir($finalDirectory, 0777);
			}
			
			// start moving image
			$_image = new Varien_Image($path . $file);
            $_image->constrainOnly(true);
            $_image->keepAspectRatio(true);
            $_image->keepFrame(true);
            $_image->keepTransparency(true);
            //$_image->resize($width, $height);
            $_image->save($finalDirectory . $newFileName . '.' . $ext);
			// delete temp directory
			unlink($path . $file);
			// return new file name
			return DS . $newFileName[0] . DS . $newFileName[1] . DS . $newFileName . '.' . $ext;
			
		} else {
			
			return false;
		}
	}

	public function removeSpecialCharacters($string = false)
    {
        if ($string) {
            $string = str_replace(array('_'), '-', $string);
            $string = str_replace(array('[\', \']'), '', $string);
            $string = preg_replace('/\[.*\]/U', '', $string);
            $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
            //$string = htmlentities($string, ENT_COMPAT, 'utf-8');
            $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
            $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
            $string = strtolower(trim($string, '-'));
        }
        
        return $string;
    }
    
    public function removeNonEmptyDir($dir)
    {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
           }
         }
         reset($objects);
         rmdir($dir);
       }
     }

    public function coveruploadAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('conturifurnizori/setari/profil/');
        }
        
        return;
    }
    
    public function logouploadAction()
    {
        if ($this->getRequest()->isPost()) {
            
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->_redirect('conturifurnizori/setari/profil/');
        }
        
        return;
    }
    
    /*** removed ***/
    // public function serviciiAction()
    // {
        // /**
         // * Check if customer is furnizor or not
         // * If it is not furnizor, redirect him to customer account dashboard
         // */
        // $this->_checkFurnizoriGroup();
//         
        // /**
         // * continue if customer is furnizor
         // */
        // $this->loadLayout();
//         
        // $this->_initLayoutMessages('customer/session');
        // $this->_initLayoutMessages('catalog/session');
//         
        // $this->getLayout()->getBlock('head')->setTitle($this->__('Detalii servicii'));
        // $this->renderLayout();
    // }
    
    /*** removed ***/
    // public function reteleAction()
    // {
        // /**
         // * Check if customer is furnizor or not
         // * If not logged in then redirect to customer account dashboard
         // */
        // $this->_checkFurnizoriGroup();
//         
        // /**
         // * continue if customer is furnizor
         // */
        // $this->loadLayout();
//         
        // $this->_initLayoutMessages('customer/session');
        // $this->_initLayoutMessages('catalog/session');
//         
        // $this->getLayout()->getBlock('head')->setTitle($this->__('Retele socializare'));
        // $this->renderLayout();
    // }
}
