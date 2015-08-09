<?php
class Picode_ConturiFurnizori_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{
    /**
     * override the _prepareCollection to add an other attribute to the grid
     * @return $this
     */
    protected function _prepareCollection(){
        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email')
            ->addAttributeToSelect('created_at')
            ->addAttributeToSelect('group_id')
            //if the attribute belongs to the customer, use the line below
            ->addAttributeToSelect('business_descriptions_title')
            ->addAttributeToSelect('furnizor_account_status')
            ->addAttributeToSelect('furnizor_account_type')
            ->addAttributeToSelect('provider_reputation')
            ->addAttributeToSelect('furnizor_location_city')
            ->addAttributeToSelect('furnizor_location_province')
            //if the attribute belongs to the customer address, comment the line above and use the one below
            //->joinAttribute('mobile', 'customer_address/mobile', 'default_billing', null, 'left')
            //->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            //->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
            //->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            //->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
            //->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ;

        $this->setCollection($collection);
        //code from Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
        //since calling parent::_prepareCollection will render the code above useless
        //and you cannot call in php parent::parent::_prepareCollection()
        if ($this->getCollection()) {

            $this->_preparePage();

            $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $dir      = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $filter   = $this->getParam($this->getVarNameFilter(), null);

            if (is_null($filter)) {
                $filter = $this->_defaultFilter;
            }

            if (is_string($filter)) {
                $data = $this->helper('adminhtml')->prepareFilterString($filter);
                $this->_setFilterValues($data);
            }
            else if ($filter && is_array($filter)) {
                $this->_setFilterValues($filter);
            }
            else if(0 !== sizeof($this->_defaultFilter)) {
                $this->_setFilterValues($this->_defaultFilter);
            }

            if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
                $dir = (strtolower($dir)=='desc') ? 'desc' : 'asc';
                $this->_columns[$columnId]->setDir($dir);
                $this->_setCollectionOrder($this->_columns[$columnId]);
            }

            if (!$this->_isExport) {
                $this->getCollection()->load();
                $this->_afterLoadCollection();
            }
        }

        return $this;
    }

    /**
     * override the _prepareColumns method to add a new column after the 'email' column
     * if you want the new column on a different position just change the 3rd parameter
     * of the addColumn method to the id of your desired column
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'width'     => '50px',
            'index'     => 'entity_id',
            'type'  => 'number',
        ));
        
        $this->addColumn('name', array(
            'header'    => Mage::helper('customer')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('email', array(
            'header'    => Mage::helper('customer')->__('Email'),
            'width'     => '150',
            'index'     => 'email'
        ));

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $this->addColumn('group', array(
            'header'    =>  Mage::helper('customer')->__('Group'),
            'width'     =>  '100',
            'index'     =>  'group_id',
            'type'      =>  'options',
            'options'   =>  $groups,
        ));
        
        $this->addColumn(
            'business_descriptions_title',
            array(
                'header'  => 'Denumire Comerciala',
                'index'   => 'business_descriptions_title',
                'type'    => 'text',
            )
        );
        
        $this->addColumn('furnizor_location_city', array(
            'header'    => Mage::helper('customer')->__('Localitate'),
            'width'     => '200',
            'index'     => 'furnizor_location_city'
        ));
        
        $options = Mage::getModel('conturifurnizori/attribute_source_province')->getOptionArray();
            
        $this->addColumn(
            'furnizor_location_province',
            array(
                'header'  => 'Judet',
                'index'   => 'furnizor_location_province',
                'type'    => 'options',
                'options' => $options
            )
        );
        
        $options = Mage::getModel('conturifurnizori/attribute_source_status')->getOptionArray();
            
        $this->addColumn(
            'furnizor_account_status',
            array(
                'header'  => 'Account Status',
                'index'   => 'furnizor_account_status',
                'type'    => 'options',
                'options' => $options
            )
        );
        
        $options = Mage::getModel('conturifurnizori/attribute_source_acctype')->getOptionArray();
        
        $this->addColumn(
            'furnizor_account_type',
            array(
                'header'  => 'Account Type',
                'index'   => 'furnizor_account_type',
                'type'    => 'options',
                'options' => $options
            )
        );

        $this->addColumn('provider_reputation', array(
            'header'    => Mage::helper('customer')->__('RPP Points'),
            'width'     => '100',
            'type'      => 'number',
            'index'     => 'provider_reputation'
        ));
        
        $this->addColumn('customer_since', array(
            'header'    => Mage::helper('customer')->__('Customer Since'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index'     => 'created_at',
            'gmtoffset' => true
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
        //return parent::_prepareColumns();
        
        //return parent::_prepareColumns();
        return $this;
    }
}