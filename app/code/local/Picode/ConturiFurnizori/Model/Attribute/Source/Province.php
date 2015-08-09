<?php
class Picode_ConturiFurnizori_Model_Attribute_Source_Province extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
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
                    'label' => Mage::helper('eav')->__('Alba'),
                    'value' => 278
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Arad'),
                    'value' => 279
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Arges'),
                    'value' => 280
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Bacau'),
                    'value' => 281
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Bihor'),
                    'value' => 282
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Bistrita Nasaud'),
                    'value' => 283
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Botosani'),
                    'value' => 284
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Brasov'),
                    'value' => 285
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Braila'),
                    'value' => 286
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Bucuresti'),
                    'value' => 287
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Buzau'),
                    'value' => 288
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Caras-Severin'),
                    'value' => 289
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Calarasi'),
                    'value' => 290
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Cluj'),
                    'value' => 291
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Constanta'),
                    'value' => 292
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Covasna'),
                    'value' => 293
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Dambovita'),
                    'value' =>294
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Dolj'),
                    'value' => 295
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Galati'),
                    'value' => 296
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Giurgiu'),
                    'value' => 297
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Gorj'),
                    'value' => 298
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Harghita'),
                    'value' => 299
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Hunedoara'),
                    'value' => 300
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Ialomita'),
                    'value' => 301
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Iasi'),
                    'value' => 302
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Ilfov'),
                    'value' => 303
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Maramures'),
                    'value' => 304
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Mehedinti'),
                    'value' => 305
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Mures'),
                    'value' => 306
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Neamt'),
                    'value' => 307
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Olt'),
                    'value' => 308
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Prahova'),
                    'value' => 309
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Satu-Mare'),
                    'value' => 310
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Salaj'),
                    'value' => 311
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Sibiu'),
                    'value' => 312
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Suceava'),
                    'value' => 313
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Teleorman'),
                    'value' => 314
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Timis'),
                    'value' => 315
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Tulcea'),
                    'value' => 316
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Vaslui'),
                    'value' => 317
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Vâlcea'),
                    'value' => 318
                ),
    
                array(
                    'label' => Mage::helper('eav')->__('Vrancea'),
                    'value' => 319
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
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionValueArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['value'];
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