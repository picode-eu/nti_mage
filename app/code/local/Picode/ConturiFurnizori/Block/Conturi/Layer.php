<?php
class Picode_ConturiFurnizori_Block_Conturi_Layer extends Picode_ConturiFurnizori_Block_Conturi_Providers
{
    protected $_collection;

    protected $_params;

    public function __construct()
    {
        if (Mage::app()->getRequest()->getActionName() == 'provider') {
            // Picode_MiniAdvancedSearch_Block_Results
            $block = Mage::app()->getLayout()->createBlock('miniadvancedsearch/results');
            $this->_collection = $block->getQuickSearchResult();
        } else {
            // Picode_ConturiFurnizori_Block_Conturi_Providers
            $this->_collection = parent::_getProductCollection();
        }

        $this->_params = $this->getRequest()->getParams();

        Mage::register('params', $this->_params);
        Mage::getSingleton('core/session')->setParams($this->_params);
    }

    public function getLoadedCollection()
    {
        return $this->_collection;
    }

    public function getFilterAttributes()
    {
        foreach ($this->_filterAttributes as $attribute) {
            $details = Mage::getSingleton('eav/config')->getAttribute('customer', $attribute);
            // $attributDatails[$attribute] = array(
                                               // 'label' => $details->getFrontendLabel(),
                                               // 'friendly_url' => strtolower(str_replace(' ', '-', $details->getFrontendLabel())),
                                           // );
            $attributDatails[$attribute] = $details;
        }

        return $attributDatails;
    }

    public function getAttributeOptions($attribute)
    {
        if ($attribute->usesSource()) {
            return $attribute->getSource()->getAllOptions(false);
        }
    }

    public function addFilterToCollection($attribute, $newValue)
    {
        $params = $this->_params;
        $attribute = str_replace(' ', '-', $attribute);
        $url = $this->getBaseUrl() . 'conturifurnizori/furnizori/?';

        if (Mage::app()->getRequest()->getActionName() == 'provider') {
            $url = $this->getBaseUrl() . 'miniadvancedsearch/search/provider/?';
        }

        // remove non-customer attribute params
        unset($params['p']);

        if (Mage::app()->getRequest()->getActionName() == 'provider') {
            unset($params['search_for'], $params['region'], $params['services']);
            unset($params['prices'], $params['experienta'], $params['denumire']);
        }

        // add filtrable params to url
        $i = 0;
        foreach ($params as $key => $value) {
            $i > 0 ? $url .= '&' : '';
            $url .= $key . '=' . $value;
            $i++;
        }

        $url .= count($params) ? '&' : '';
        $url .= $attribute . '=' . $newValue;

        return $url;
    }

    public function countProviders($attrCode, $attrValue)
    {
        $count = 0;
        $collection = $this->getLoadedCollection();
        // reset loaded product collection
        $collection->getSelect()->reset(Zend_Db_Select::LIMIT_COUNT);
        $collection->getSelect()->reset(Zend_Db_Select::LIMIT_OFFSET);
        $collection->clear();
        $collection->setPageSize(false);

        $collection->load();

        foreach ($collection as $_provider) {
            if ($_provider->getData($attrCode) == $attrValue) {
                $count++;
            }
        }

        //return $collection->count();
        return $count;
    }

    public function isFiltered()
    {
        $params = $this->_params;
        $attributCodes = array();
        unset($params['dir'], $params['order'], $params['judet']);

        if (Mage::app()->getRequest()->getActionName() == 'provider') {
            unset($params['search_for'], $params['region'], $params['services']);
            unset($params['prices'], $params['experienta'], $params['denumire']);
        }

        foreach ($params as $key => $val) {
            $attributCodes[] = $this->convertAttributeLabeToCode($key);
        }

        if (array_intersect($attributCodes, $this->_filterAttributes)) {
            return true;
        }

        return;
    }

    public function isCodeFiltered($code)
    {
        $params = $this->_params;
        $attributCodes = array();
        unset($params['dir'], $params['order'], $params['judet']);

        foreach ($params as $key => $val) {
            if ($key == $code) {
                return true;
            }
        }

        return false;
    }

    public function getFilteredAttributes()
    {
        $params = $this->_params;
        $attributCodes = array();
        unset($params['dir'], $params['order'], $params['judet']);

        if (Mage::app()->getRequest()->getActionName() == 'provider') {
            unset($params['search_for'], $params['region'], $params['services']);
            unset($params['prices'], $params['experienta'], $params['denumire']);
        }

        foreach ($params as $key => $val) {
            $attributCodes[$this->convertAttributeLabeToCode($key)] = $val;
        }

        return $attributCodes;
    }

    public function getAttributeDetails($attribute)
    {
        return Mage::getSingleton('eav/config')->getAttribute('customer', $attribute);
    }

    public function clearUrl($string)
    {
        $cleared = str_replace($string, '', $this->helper('core/url')->getCurrentUrl());

        return trim(str_replace('&&', '&', $cleared), '&');

    }

    public function getAttributeText($attribute, $value)
    {
        if ($attribute->usesSource()) {
            return $attribute->getSource()->getOptionText($value);
        }
    }

    public function removeAllFilterUrl()
    {
        $params = $this->_params;
        $paramsCount = count($params);
        $url = $this->getBaseUrl() . 'conturifurnizori/furnizori/';

        if (Mage::app()->getRequest()->getActionName() == 'provider') {
            $url = $this->getBaseUrl() . 'miniadvancedsearch/search/provider/';
        }

        $i = 0;

        foreach ($params as $key => $val) {
            if ($key != 'dir' && $key != 'order' && $key != 'judet') {
                unset($params[$key]);
            } elseif (count($params) > 1 && $i == 0) {
                $url .= '?' . $key . '=' . $val;
                $i++;
            } elseif ($i > 0) {
                $url .= '&' . $key . '=' . $val;
                $i++;
            }
        }

        return $url;
    }

}
