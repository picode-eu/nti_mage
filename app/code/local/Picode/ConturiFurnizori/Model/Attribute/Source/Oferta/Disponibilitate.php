<?php
class Picode_ConturiFurnizori_Model_Attribute_Source_Oferta_Disponibilitate extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
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
                    'label' => Mage::helper('eav')->__('sub 2 ore'),
                    'value' => 'sub-2'
                ),

                array(
                    'label' => Mage::helper('eav')->__('2-3 ore'),
                    'value' => '2-3'
                ),
                array(
                    'label' => Mage::helper('eav')->__('3-4 ore'),
                    'value' => '3-4'
                ),
                array(
                    'label' => Mage::helper('eav')->__('5-6 ore'),
                    'value' => '5-6'
                ),
                array(
                    'label' => Mage::helper('eav')->__('7-8 ore'),
                    'value' => '7-8'
                ),
                array(
                    'label' => Mage::helper('eav')->__('9-10 ore'),
                    'value' => '9-10'
                ),
                array(
                    'label' => Mage::helper('eav')->__('maxim 12 ore'),
                    'value' => 'max-12'
                ),
                array(
                    'label' => Mage::helper('eav')->__('maxim 14 ore'),
                    'value' => 'max-14'
                ),
                array(
                    'label' => Mage::helper('eav')->__('maxim 16 ore'),
                    'value' => 'max-16'
                ),
                array(
                    'label' => Mage::helper('eav')->__('maxim 20 ore'),
                    'value' => 'max-20'
                ),
                array(
                    'label' => Mage::helper('eav')->__('cat e nevoie'),
                    'value' => 'cat-e-nevoie'
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