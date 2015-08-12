<?php

/**
 * This class initializes the form block to use in checkout process 
 *
 */
class EuroPayment_EuPlatesc_Block_Initialize_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('ep/initialize/form.phtml');
        parent::_construct();
    }
}