<?php
class Picode_ConturiFurnizori_Model_Attribute_Source_Experience extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
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
                    'label' => Mage::helper('eav')->__('Peste 10 ani'),
                    'value' => '10',
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('9 ani'),
                    'value' => '9',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('8 ani'),
                    'value' => '8',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('7 ani'),
                    'value' => '7',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('6 ani'),
                    'value' => '6',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('5 ani'),
                    'value' => '5',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('4 ani'),
                    'value' => '4',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('3 ani'),
                    'value' => '3',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('2 ani'),
                    'value' => '2',
                ),
                
                array(
                    'label' => Mage::helper('eav')->__('1 an'),
                    'value' => '1',
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
}