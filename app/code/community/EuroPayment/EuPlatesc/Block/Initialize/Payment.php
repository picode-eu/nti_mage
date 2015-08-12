<?php

class EuroPayment_EuPlatesc_Block_Initialize_Payment extends Mage_Core_Block_Abstract
{
    protected function _construct()
    {
        parent::_construct();
    }
    
    protected function _toHtml()
    {
		$standard = Mage::getModel('ep/initialize');
		$code = 'payment_block_' . $standard->getCode();

    	$html = parent::_toHtml();
		$html.= '<p class="redirect-payment">' . $this->__('Sunteţi redirecţionat către modulul de plată, unde veţi putea introduce detaliile cardului dvs pentru a putea finaliza tranzacţia.') . "</p>";
		$html.= '<form name="' . $code . '" id="' . $code . '" method="POST" action="' . $standard->getUrl() . ($this->getNewwindow() ? (' target="' . strtoupper($code) . '"') : '') . '">';

		$formFields = $standard->getCheckoutFormFields();
		if (is_array($formFields))
        foreach ($formFields as $field => $value)
        {
			if(is_array($value))
	    	{
	    		if (count($value))
	    		{
					foreach($value as $subvalue)
					{
						$html .= '<input type="hidden" name="'.$field.'[]" value="'.$subvalue.'" />' . "\n";
					}
	    		}
	    	}
	    	else
	    	{
				$html .= '<input type="hidden" name="'.$field.'" value="'.$value.'" />' . "\n";
	    	}
        }
        $html.= '<p class="redirect-payment-button"><button type="submit">' 
        	. $this->__('Finalizează comanda') . '<sup>*</sup></button></p> <p class="redirect-payment"><sup>*</sup>' 
        	. $this->__('Daca nu sunteti redirectionat automat apasati "Finalizeaza comanda" pentru a putea finaliza tranzactia.')
        	. '</p></form>' . "\n";
        
        if ('www' == substr($_SERVER['HTTP_HOST'], 0, 3)) // activate self-submit if we're on www....
        {
			$html.= '<script type="text/javascript">document.getElementById("' . $code . '").submit();</script>';
        }

        return $html;
    }
}
