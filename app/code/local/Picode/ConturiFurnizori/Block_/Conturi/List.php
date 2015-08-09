<?php   
class Picode_ConturiFurnizori_Block_Conturi_List extends Mage_Catalog_Block_Product_List
{
    protected $_conturiCollection = null;
    
    public function __construct()
    {
        $model = Mage::getModel('catalog/product');
        
        $conturiCollection = $model->getCollection()
                ->addAttributeToSelect('*')
                //->addAttributeToFilter('status', 1)
                ->addAttributeToFilter('visibility', array('eg' => 4))
                ->addFieldToFilter('type_id', 'conturifurnizori');
                
        $this->_conturiCollection = $conturiCollection;
    }
    
    public function getConturiCollection()
    {
        return $this->_conturiCollection;
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
    
    public function getOptionPrices($_product)
    {
        $productModel = Mage::getModel('catalog/product');
        $_product = $productModel->load($_product->getId());
        $options = $_product->getOptions();
        $optionPrices = array();
        
        foreach ($options as $option) {
            if ($option->getType() == 'drop_down') {
                $values = $option->getValues();
                
                foreach ($values as $value) {
                    $period = $value->getTitle();
                    $optionPrices[] = $value->getPrice();
                }
            }
        }
        
        return $optionPrices;
    }
    
    /*** providers list ****/
    
    private function _getAllProviders()
    {
        $privedersCollection = Mage::getModel('customer/customer')
                    ->getCollection()
                    ->addAttributeToFilter('group_id', 4);
                    
        return $privedersCollection;
    }
	
	public function getProvidersLastAdded($back, $limit = false)
	{
		$privedersCollection = $this->_getAllProviders()
                ->addAttributeToSelect('*')
        		->addAttributeToFilter('furnizor_account_status', 1)
				->addAttributeToFilter('furnizor_account_online_status', 1)
                ->addAttributeToFilter('business_images_logo', array('neq' => 'NULL'))
                ->addAttributeToFilter('business_descriptions_desc', array('neq' => 'NULL'))
                //->addAttributeToFilter('furnizor_account_level', 2)
				->setOrder('created_at', 'DESC');
				
		if ($back) {
			$endDate = date('Y-m-d 00:00:00', Mage::getModel('core/date')->timestamp(time()));
			$startDate = date('Y-m-d 00:00:00', strtotime($endDate . ' - ' . $back . ' day'));
			
			$privedersCollection->addFieldToFilter('created_at', array(array('from' => $startDate, 'to' => $endDate)));
		}
		
        // echo $privedersCollection->getSelect();
        // echo $privedersCollection->getSize();
				
		if ($limit) {
			$privedersCollection->getSelect()->limit($limit);
		}
        
		return $privedersCollection;
	}

    public function getTopProviders($back, $limit = false)
    {
        $privedersCollection = $this->_getAllProviders()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('furnizor_account_status', array('in' => 1,2))
                ->addAttributeToFilter('furnizor_account_online_status', 1)
                ->addAttributeToFilter('business_images_logo', array('neq' => 'NULL'))
                ->addAttributeToFilter('business_descriptions_desc', array('neq' => 'NULL'))
                ->addAttributeToFilter('furnizor_account_type', 2)
                ->addAttributeToFilter('furnizor_account_level', 2)
                ->setOrder('provider_reputation', 'DESC')
                ;
                
        if ($back) {
            $endDate = date('Y-m-d 00:00:00', Mage::getModel('core/date')->timestamp(time()));
            $startDate = date('Y-m-d 00:00:00', strtotime($endDate . ' - ' . $back . ' day'));
            
            $privedersCollection->addFieldToFilter('created_at', array(array('from' => $startDate, 'to' => $endDate)));
        }
        
        // echo $privedersCollection->getSelect();
        // echo $privedersCollection->getSize();
                
        if ($limit) {
            $privedersCollection->getSelect()->limit($limit);
        }
        
        return $privedersCollection;
    }
    
    public function getProviderDetails($prioviderId)
    {
        return mage::getModel('customer/customer')->load($prioviderId);
    }
    
    public function resizeImage($img, $width, $height = false)
    {
         // /echo $width; die('in');
         if(!$height) $height = $width;
        
         $_media_dir = Mage::getBaseDir('media') . DS . 'customer' . DS;
         $cache_dir = $_media_dir . 'cache' . DS . $width . 'x' .$height;
                  
         if(file_exists($_media_dir . $img)){
             
              if(!is_dir($cache_dir)){
                   mkdir($cache_dir);
              }
    
              $_image = new Varien_Image($_media_dir . $img);
              $_image->constrainOnly(true);
              $_image->keepAspectRatio(true);
              $_image->keepFrame(true);
              $_image->keepTransparency(true);
              $_image->resize($width, $height);
              $_image->save($cache_dir . $img);
    
              return Mage::getBaseUrl('media') . 'customer' . DS . 'cache' . DS . $width . 'x' .$height . $img;
         }
        
         return false;
    }
    
}