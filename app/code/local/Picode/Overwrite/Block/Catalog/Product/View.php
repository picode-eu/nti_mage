<?php
class Picode_Overwrite_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Fix "Call to a member function getMetaTitle() on a non-object" error
     */
    protected function _prepareLayout()
    {
        $this->getLayout ()->createBlock ('catalog/breadcrumbs');
        $headBlock = $this->getLayout ()->getBlock ('head');

        if ($headBlock) {
            $product = $this->getProduct();

            if ($product) {
                $title = $product->getMetaTitle();
                if ($title) {
                    $headBlock->setTitle ($title);
                }
                $keyword = $product->getMetaKeyword ();
                $currentCategory = Mage::registry ('current_category');
                if ($keyword) {
                    $headBlock->setKeywords ($keyword);
                } elseif ($currentCategory) {
                    $headBlock->setKeywords ($product->getName ());
                }
                $description = $product->getMetaDescription ();
                if ($description) {
                    $headBlock->setDescription (($description));
                } else {
                    $headBlock->setDescription (Mage::helper ('core/string')->substr ($product->getDescription (), 0, 255));
                }
                if ($this->helper ('catalog/product')->canUseCanonicalTag ()) {
                    $params = array('_ignore_category' => true);
                    $headBlock->addLinkRel ('canonical', $product->getUrlModel ()->getUrl ($product, $params));
                }
            }
        }
    }

    /**
     * get a human readable file size
     * @return string
     */
    public function formatFilesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
    
    /**
     * get attributes details from a specified set/info
     * @return object
     */
    public function getProductAttributesBySet($_product, $attrSet = false, $attrGroup = false)
    {
        //Zend_Debug::dump($_product->getData());
        if (!$attrSet && !$attrInfo) {
            //return Mage::getResourceSingleton('catalog/product')->loadAllAttributes()->getAttributesByCode();
            return;
        } else {
            //$productAttributes = Mage::getResourceSingleton('catalog/product')->loadAllAttributes()->getAttributesByCode();
            $productAttributes = Mage::getResourceSingleton('catalog/product')->loadAllAttributes()->getAttributesById();

            $i = 0;
            foreach ($productAttributes as $attribute) {
                if ($attribute->getAttributeSetId() == $attrSet && $attribute->getAttributeGroupId() == $attrGroup && $attribute->getIsVisibleOnFront()) {
                    //$attributeText = $_product->getAttributeText($attribute->getAttributeCode());
                    if ($attribute->getFrontendInput() == 'select') {
                        $value = $_product->getData($attribute->getAttributeCode());
                        $attributeText = $value != '' ? $attribute->getSource()->getOptionText($value) : $value;
                    } elseif ($attribute->getFrontendInput() == 'boolean') {
                    	$attributeText = $_product->getData($attribute->getAttributeCode()) ? 'Da' : 'Nu';
					} elseif ($attribute->getFrontendInput() == 'textarea') {
						// skip descriptions
						continue;
                    } else {
                        if ($attribute->getAttributeCode() == 'ofg_zona_personalizata') {
                            $zoneText = array();
                            $zoneArr = explode(',', $_product->getData('ofg_zona_personalizata'));
                            foreach ($zoneArr as $zone) {
                                $zoneText[] = Mage::getModel('directory/region')->load($zone)->getName();
                            }
                            $attributeText = implode(', ', $zoneText);
                        } else {
                            $attributeText = $_product->getData($attribute->getAttributeCode());
                        }
                    }
					
					// $attr = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute->getAttributeCode());
                	//Zend_Debug::dump($attributeText); 
                    
                    if ($attributeText != '') {
                        if (is_array($attributeText)) {
                            $attributeText = implode(', ', $attributeText);
                        }
                        
						if ($attribute->getFrontendInput() == 'textarea') {
							// move descriptions at the bottom of the array
							$attributes[$i + 100]['title'] = $attribute->getFrontendLabel();
                        	$attributes[$i + 100]['value'] = $attributeText;
						} else {
							$attributes[$i]['title'] = $attribute->getFrontendLabel();
                        	$attributes[$i]['value'] = $attributeText;
						}
                        $i++;
                    }
                }
                
            }
        }

		ksort($attributes);
        
		// Zend_Debug::dump($attributes);
		// die('end');
        return $attributes;
    }

	public function getProviderAttributesBySet($_provider, $attrPrefix = false)
	{
		$i = 0;
		$attributes = array();
		
		foreach ($_provider->getData() as $key => $value) {
			//echo $key . '<br />';
			if (strpos($key, $attrPrefix) !== false && $value != '') {
				$attribute = $_provider->getResource()->getAttribute($key);
				if ($attribute->getFrontendInput() == 'textarea') {
					// skip descriptions
					continue;
                } else {
                	$attributes[$i]['title'] = $_provider->getResource()->getAttribute($key)->getStoreLabel();;
	                $attributes[$i]['value'] = $value;
					$i++;
                }
				
			}
		}
		
		return $attributes;
	}
    
    public function getSimilarOffers($_product)
    {
        $minPrice = $_product->getFinalPrice() * 0.9;
        $maxPrice = $_product->getFinalPrice() * 1.10;
        $availabilities = explode(',', $_product->getOfgZonaPersonalizata());
        
        //echo '$availabilities'; Zend_Debug::dump($availabilities);
        
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('ofg_zona_personalizata')
            ->addAttributeToSelect('ofg_tip_oferta')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('url_path')
            ->addAttributeToSelect('small_image')
            ->addAttributeTofilter('status', 1)
            ->addAttributeTofilter('attribute_set_id', $_product->getAttributeSetId())
            ->addAttributeTofilter('ofg_tip_oferta', $_product->getOfgTipOferta())
            //->addFieldToFilter('price', array(array('from' => $minPrice, 'to' => $maxPrice))) // doesn't work
            //->addAttributeTofilter('entity_id', array('neg', $_product->getEntityId()))
            ->setOrder('price', 'desc');
            
        $collection->getSelect()->limit(8);
        //echo $collection->getSelect();
        
        foreach ($collection as $key => $_similar) {
            $sAvailabilities = explode(',', $_similar->getOfgZonaPersonalizata());
            $matches = array_intersect($availabilities, $sAvailabilities);
            
            if ($_product->getId() == $_similar->getId() || count($matches) == 0 || $_similar->getFinalPrice() < $minPrice || $_similar->getFinalPrice() > $maxPrice) {
                $collection->removeItemByKey($key);
            }
        }
        
        return $collection;
    }

    public function getSameProviderOffers($_product)
    {
        $collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('url_path')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('ofg_tip_oferta')
            ->addAttributeTofilter('status', 1)
            ->addAttributeTofilter('ofg_customer_id', $_product->getOfgCustomerId())
            ->setOrder('price', 'desc');
            
        $collection->getSelect()->limit(8);
        //echo $collection->getSelect();
        
        foreach ($collection as $key => $_similar) {
            if ($_product->getId() == $_similar->getId()) {
                $collection->removeItemByKey($key);
            }
        }
        
        return $collection;
    }

	public function truncate($text, $length)
	{
	   $length = abs((int)$length);
	   if(strlen($text) > $length) {
	      $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
	   }
	   return($text);
	}

    public function getPriceAverage($_product)
    {
        $productModel = Mage::getModel('catalog/product');
        $product = $productModel->load($_product->getId());
        $options = $product->getOptions();
        $priceAverage = array();
        
        foreach ($options as $option) {
            if ($option->getType() == 'drop_down') {
                $values = $option->getValues();
                
                foreach ($values as $value) {
                    $period = $value->getTitle();
                    $price = $value->getPrice();
                    $priceAverage[] = $price / $period;
                }
            }
        }
        
        return $priceAverage;
    }
}


