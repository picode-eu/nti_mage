<?php
class Picode_Overwrite_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{
	public function sortByReview($dir)
	{
	    $corelation = 't_' . uniqid();
        
		// if (Mage::app()->getRequest()->getModuleName() == 'catalog')
		// {
			$table = $this->getTable('review/review');
		
	        $entityCodeId = Mage::getModel('review/review')->getEntityIdByCode(Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE);
	        $cond = $this->getConnection()->quoteInto($corelation . '.entity_pk_value = e.entity_id and ','') . $this->getConnection()->quoteInto($corelation . '.entity_id = ? ',$entityCodeId);
			
	        $this->getSelect()
	        	->joinLeft(array($corelation => $table), $cond, array('review' => new Zend_Db_Expr('count(review_id)')))
	        	->group('e.entity_id')
	        	->order("review $dir");
		// }
    }
	
	public function sortByPopularity($dir)
	{
	    $corelation = 't_' . uniqid();
        
		// if (Mage::app()->getRequest()->getModuleName() == 'catalog')
		// {
			$table = $this->getTable('reputationpoints/offer');
			
			$this->getSelect()
					->joinLeft(
						array($corelation => $table), 
						"e.entity_id = $corelation.entity_id",
						array("reputation_points" => "$corelation.reputation_points")
					)
					->where("$corelation.reputation_points is not null")
					->order("reputation_points $dir");
                    
		// }
    }
    
    public function sortByReputation($dir)
    {
        $corelation = 'provider_reputation_' . uniqid();
        $corelation_p = 'product_varchar_' . uniqid();
        
        $table = $this->getTable('reputationpoints/reputation');
        $table_p = $this->getTable('catalog_product_entity_varchar');
        
        $this->getSelect()
                ->joinLeft(
                    array($corelation_p => $table_p), 
                    "e.entity_id = $corelation_p.entity_id", 
                    array("ofg_customer_id" => "$corelation_p.value")
                )
                ->where("$corelation_p.attribute_id = ?", "309")
                
                ->joinLeft(
                    array($corelation => $table),
                    // "e.entity_id = $corelation.provider_id",
                    "$corelation_p.value = $corelation.provider_id", 
                    array("earned_points" => "$corelation.earned_points")
                )
                ->where("$corelation.earned_points is not null")
                ->order("earned_points $dir")
                ;
          
        // echo $this->getSelect();
        // Zend_Debug::dump($this->getData());
        // die();
    }
    
    // public function sortByProvider($dir)
    // {
        // $corelation = 't_' . uniqid();
//         
        // // if (Mage::app()->getRequest()->getModuleName() == 'catalog')
        // // {
            // $table = $this->getTable('customer/entity');
//             
            // $this->getSelect()
                    // ->joinLeft(
                        // array($corelation => $table), 
                        // "e.ofg_customer_id = $corelation.customer_id", 
                        // array("furnizor_account_type" => "$corelation.furnizor_account_type")
                    // )
                    // ->where("$corelation.furnizor_account_type is not null")
                    // ->order("furnizor_account_type $dir");
        // // }
    // }
    
    public function sortByLoves($dir)
    {
        $corelation = 't_' . uniqid();
        
        // if (Mage::app()->getRequest()->getModuleName() == 'catalog')
        // {
        	$table = $this->getTable('popularity/offer');
			
            $this->getSelect()
                    ->joinLeft(
                        array($corelation => $table), 
                        "e.entity_id = $corelation.offer_id", 
                        array("loves" => "$corelation.loves")
                    )
                    ->where("$corelation.loves is not null")
                    ->order("loves $dir");
        // }
    }
	
	
}
