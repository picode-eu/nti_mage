<?php
class Picode_ConturiFurnizori_Model_Attribute_Source_Zone extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
            
                array(
                    'label' => Mage::helper('eav')->__('Selecteaza'),
                    'value' => '',
                    'selected' => 'selected'
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Local'),
                    'value' => 1
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Doar in judet'),
                    'value' => 2
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Judetele limitrofe'),
                    'value' => 3
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Toata tara'),
                    'value' => 4
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('Personalizat'),
                    'value' => 5
                ),
                
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            'type'      => 'tinyint(1)',
            'unsigned'  => false,
            'is_null'   => true,
            'default'   => null,
            'extra'     => null
        );

        return $columns;
    }

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = array();

        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array(
            'type'      => 'index',
            'fields'    => array($this->getAttribute()->getAttributeCode())
        );

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
    
    public function getZoneNeighboring()
    {
       $neighboring = array(
            
            '278' => '278,291,306,312,317,300,279,282',
            '279' => '279,282,278,300,315',
            '280' => '280,318,312,285,294,314,308',
            '281' => '281,317,319,293,299,307',
            '282' => '282,310,311,291,278,279',
            '283' => '283,291,304,313,306',
            '284' => '284,302,313',
            '285' => '285,293,288,309,294,280,312,306,299',
            '286' => '286,296,316,292,301,288,319',
            '287' => '287,303,301,288,309,294,297,290',
            '288' => '288,285,309,293,301,296,319',
            '289' => '289,315,300,298,305',
            '290' => '290,292,303,297,301',
            '291' => '291,311,304,283,306,278,282',
            '292' => '292,316,290,301,286',
            '293' => '293,281,288,319,285,299',
            '294' => '294,285,309,303,297,314,280',
            '295' => '295,305,298,318,308',
            '296' => '296,317,319,286,316',
            '297' => '297,294,303,314,290',
            '298' => '298,305,289,300,318,295',
            '299' => '299,307,281,306,285,293,313',
            '300' => '300,315,279,278,318,298,289',
            '301' => '301,303,309,288,286,292,290',
            '302' => '302,307,284,313,317',
            '303' => '303,287,301,288,309,294,297,290',
            '304' => '304,310,311,291,283,313',
            '305' => '305,289,298,295',
            '306' => '306,291,283,313,299,312,278',
            '307' => '307,299,313,302,317,281',
            '308' => '308,318,280,314,295',
            '309' => '309,285,288,301,294',
            '310' => '310,282,311,306',
            '311' => '311,310,304,282,291',
            '312' => '312,285,278,280,318,306',
            '313' => '313,304,283,306,299,307,302,284',
            '314' => '314,308,280,294,297',
            '315' => '315,279,300,289',
            '316' => '316,296,286,292',
            '317' => '317,302,307,281,319,296',
            '318' => '318,278,312,280,308,295,298,300',
            '319' => '319,317,296,286,288,293,281',
            
        );
        
        return $neighboring;
    }
}