<?php
class Picode_ConturiFurnizori_Model_Attribute_Source_Neighboring extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
            
                array(
                    'base' => 278,
                    'neighbor' => '291,304,312,317,300,279,282'
                ),
    
            );
        }
        return $this->_options;
    }
}
