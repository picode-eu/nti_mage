<?php
class Picode_Overwrite_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    protected $_collection = null;
	protected $_direction = 'desc';
	
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        
        // start offer sort orders
        if($this->getCurrentOrder() == 'review'){
            $this->_collection->sortByReview($this->getCurrentDirection());
		} elseif ($this->getCurrentOrder() == 'popularitate') {
			$this->_collection->sortByPopularity($this->getCurrentDirection());
        } elseif ($this->getCurrentOrder() == 'reputatie-furnizor') {
            $this->_collection->sortByReputation($this->getCurrentDirection());
        // } else if ($this->getCurrentOrder() == 'provider') {
            // $this->_collection->sortByProvider($this->getCurrentDirection());
        } else if ($this->getCurrentOrder() == 'loves') {
            $this->_collection->sortByLoves($this->getCurrentDirection());
            
        // start provider list sort orders
        } elseif ($this->getCurrentOrder() == 'popularitate_furnizor') {
            $this->_collection->setOrder('provider_reputation', $this->getCurrentDirection());
            // than by:
            $this->_collection->setOrder('provider_views', $this->getCurrentDirection());
            $opositDirection = $this->getCurrentDirection() == 'asc' ? 'desc' : 'asc';
            $this->_collection->setOrder('business_descriptions_title', $opositDirection);
        } elseif ($this->getCurrentOrder() == 'afisari') {
            $this->_collection->setOrder('provider_views', $this->getCurrentDirection());
            // than by:
            $this->_collection->setOrder('provider_reputation', $this->getCurrentDirection());
            $this->_collection->setOrder('business_descriptions_title', $this->getCurrentDirection());
        } elseif ($this->getCurrentOrder() == 'experienta') {
            $this->_collection->setOrder('business_descriptions_exp', $this->getCurrentDirection());
        } elseif ($this->getCurrentOrder() == 'sediu') {
            $this->_collection->setOrder('furnizor_location_province', $this->getCurrentDirection());
        } elseif ($this->getCurrentOrder() == 'nume') {
            $this->_collection->setOrder('business_descriptions_title', $this->getCurrentDirection());
            // than by:
            $this->_collection->setOrder('provider_reputation', $this->getCurrentDirection());
            $this->_collection->setOrder('provider_views', $this->getCurrentDirection());
        } elseif ($this->getCurrentOrder() == 'afisari') {
        } elseif ($this->getCurrentOrder() == 'servicii') {
            $this->_collection->setOrder('furnizor_company_services', $this->getCurrentDirection());
        
        // default (no sort order selected)
        } elseif ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }

        $this->_collection->setCurPage($this->getCurrentPage());
        
        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        
        return $this;
    }
    
//    public function getTotalNum()
//    {
//        return $this->_collection->count();
//    }
    
}