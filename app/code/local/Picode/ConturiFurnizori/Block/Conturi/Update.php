<?php
class Picode_ConturiFurnizori_Block_Conturi_Update extends Mage_Core_Block_Template
{
	/**
	 * get customer details
	 * return object
	 */
	public function getCustomer()
    {
        if ($this->helper('customer')->isLoggedIn()) {
        	//return $customer = $this->helper('customer')->getCustomer();
            return $this->helper('customer')->getCustomer();
        }
        
        return false;
    }
	
	/**
	 * get action name
	 * return string
	 */
	public function getAction()
	{
		return $this->getRequest()->getActionName();
	}
	
    /**
     * 
     */
    public function getPageTitle()
    {
        switch ($this->getAction()) {
            case 'retrimitecererea':
            case 'sendAprovelEmail':
                return 'Solicita activarea contului'; // preia datele furnizorului si  email de notificare pentru activarea contuuli
                break;
            case 'deblocheaza':
                return 'Solicita deblocarea contului'; // preia datele furnizorului si trimite email de notificare pentru investigarea cauzei blocarii
                break;
            case 'reactiveaza':
                return 'Solicita reactivarea contului'; // preia datele furnizorului si trimite email de notificare pentru reactivarea contului
                break;
        }
    }
    
    /**
     * set response message 
     */
    public function getResponse($data = false)
    {
        $customer = $this->getCustomer();
        $helper = $this->helper('conturifurnizori');
        
        switch ($this->getAction()) {
            case 'retrimitecererea': // account status: aprobare
                // preia datele furnizorului si  email de notificare pentru activarea contuuli
                $accountName = strtolower($helper->getAccountTypeNameBylLevel('cont_level', $customer->getFurnizorAccountLevel())->getName());
                $html  = '<p>Conturile <strong>Gratuite</strong> au nevoie de aprobarea adiministratorilor inainte de a fi publicate pe <a href="' . $this->getBaseUrl() . '">nuntainimagini.ro</a>. Aceasta actiune este necesara pentru a ne asigura ca solicitarile primite sunt reale, iar inregistralie nu sunt generate automat de diverse aplicatii web.</p>';
                $html .= '<p>Daca au trecut deja 24 de ore de la inregistrare si contul tau inca nu a fost activat, poti trimite o cerere de activare a contului tau de furnizor.</p>';
                $html .= '<form id="send-aproval" action="' . $this->getBaseUrl() . 'conturifurnizori/update/sendAprovelEmail/' . '" method="post">';
                $html .= '<textarea name="private-message" placeholder="Adauga mesajul tau administratorilor..."></textarea>';
                $html .= '<div class="actions"><button type="submit" class="submit-button button"><span><span>Trimite cererea</span></span></button><img class="loading" style="display: none" src="' . $this->getBaseUrl() . 'skin/frontend/picode/default/images/loader.gif" alt="" /></div>';
                $html .= '</form>';
                break;
            case 'sendAprovelEmail': // aproval email sent
                $html  = '<p>Mesajul a fost trimis.</p>';
                $html .= '<p>In cel mult 24 de ore contul tau de furnizor va fi analizat de catre administratorii <a href="' . $this->getBaseUrl() . '">nuntainimagini.ro</a>. Pana atunci finalizeaza-ti <a href="' . $this->getBaseUrl() . 'conturifurnizori/setari/profil/" title="Profil furnizor">setarile profilului</a> de furnizor si <a hfer="' . $this->getBaseUrl() . '" title="Adauga oferta">adauga ofertele</a> tale.</p>';
                $html .= '<p>Iti multumim pentru intelegere si rabdare.</p>';
                break;
            case 'deblocheaza': // account status: blocat
                // preia datele furnizorului si trimite email de notificare pentru investigarea cauzei blocarii
                $html = 'cont blocat (start deblocare)';
                break;
            case 'reactiveaza': // account status: sters
                // preia datele furnizorului si trimite email de notificare pentru reactivarea contului
                $html = 'cont sters (start reactivare)';
                break;
        }
        
        return $html;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}










