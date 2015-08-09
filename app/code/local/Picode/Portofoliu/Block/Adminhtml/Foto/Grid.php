<?php

class Picode_Portofoliu_Block_Adminhtml_Foto_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this -> setId('albumsGrid');
		$this -> setDefaultSort('album_id');
		$this -> setDefaultDir('DESC');
		$this -> setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('portofoliu/albums')->getCollection();
		//Zend_Debug::dump($collection); die();
		$collection->getSelect()->joinLeft(
              array('customer'=> 'customer_entity'),
             'customer.entity_id = main_table.customer_id',
              array('customer.email')
        );
		
		// var_dump((string) $collection->getSelect()); die();
		// SELECT `main_table`.*, `customer`.* FROM `portofoliu_albums` AS `main_table` INNER JOIN `customer_entity` AS `customer` ON customer.entity_id = main_table.customer_id
		// Zend_Debug::dump($collection->getData()); die();
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function getCustomerById($customerId)
	{
		$customerData = Mage::getModel('customer/customer')->load($customerId);
		return $customerData;
	} 

	protected function _prepareColumns()
	{
		$this -> addColumn(
			'album_id',
			 array(
			 	'header' => Mage::helper('portofoliu') -> __('ID'), 
			 	'align'  => 'right', 
			 	'width'  => '50px', 
			 	'index'  => 'album_id',
			 	'width'  => '20px', 
			 	)
			);

		$this -> addColumn(
			'album_name', 
			array(
				'header' => Mage::helper('portofoliu') -> __('Denumire'), 
				'align'  => 'left', 
				'index'  => 'album_name', 
			)
		);

		$this -> addColumn(
			'customer_id', 
			array(
				'header' => Mage::helper('portofoliu') -> __('Furnizor ID'), 
				'align'  => 'left', 
				'index'  => 'customer_id', 
				'width'  => '30px', 
			)
		);
		
		$this -> addColumn(
			'email', 
			array(
				'header' => Mage::helper('portofoliu') -> __('Furnizor Email'), 
				'align'  => 'left', 
				'index'  => 'email', 
				'width'  => '190px', 
			)
		);

		$this -> addColumn(
			'album_cover', 
			array(
				'header' => Mage::helper('portofoliu') -> __('Coperta'), 
				'align'  => 'left', 
				'index'  => 'album_cover', 
				'type'   => 'text',
			)
		);
		
		$this -> addColumn(
			'is_visible', 
			array(
				'header' => Mage::helper('portofoliu') -> __('Status'), 
				'index'  => 'is_visible', 
				'width'  => '30px',
			)
		);
		
		$this -> addColumn(
			'created_at', 
			array(
				'header' => Mage::helper('portofoliu') -> __('Creat la:'), 
				'index'  => 'created_at', 
				'type'   => 'datetime', 
				'width'  => '160px',
			)
		);

		// $this->addExportType('*/*/exportCsv', Mage::helper('portofoliu')->__('CSV'));
		// $this->addExportType('*/*/exportXml', Mage::helper('portofoliu')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this -> setMassactionIdField('album_id');
		$this -> getMassactionBlock() -> setFormFieldName('albums');

		$this -> getMassactionBlock() -> addItem(
			'delete', 
			array(
				'label' => Mage::helper('portofoliu') -> __('Delete'), 
				'url' => $this -> getUrl('*/*/massDelete'), 
				'confirm' => Mage::helper('portofoliu') -> __('Are you sure?')
			)
		);

		return $this;
	}

	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

}
