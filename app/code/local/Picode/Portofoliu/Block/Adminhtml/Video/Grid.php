<?php

class Picode_Portofoliu_Block_Adminhtml_Video_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct()
	{
		parent::__construct();
		$this -> setId('portofoliuGrid');
		$this -> setDefaultSort('video_id');
		$this -> setDefaultDir('DESC');
		$this -> setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('portofoliu/albums') -> getCollection();
		$this -> setCollection($collection);
		return parent::_prepareCollection();
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
				'header' => Mage::helper('portofoliu') -> __('Furnizor'), 
				'align'  => 'left', 
				'index'  => 'customer_id', 
				'width'  => '120px', 
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
			)
		);
		
		$this -> addColumn(
			'created_at', 
			array(
				'header' => Mage::helper('portofoliu') -> __('Creat'), 
				'index'  => 'created_at', 
				'type'   => 'datetime', 
			)
		);

		// $this->addExportType('*/*/exportCsv', Mage::helper('portofoliu')->__('CSV'));
		// $this->addExportType('*/*/exportXml', Mage::helper('portofoliu')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this -> setMassactionIdField('album_id');
		$this -> getMassactionBlock() -> setFormFieldName('portofoliu');

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
