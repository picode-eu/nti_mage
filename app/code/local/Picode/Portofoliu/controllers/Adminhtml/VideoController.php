<?php

class Picode_Portofoliu_Adminhtml_VideoController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction()
	{
		$this -> loadLayout()
			->_setActiveMenu('portofoliu/items')
			->_addBreadcrumb(Mage::helper('adminhtml')
			->__('Portofolii'), Mage::helper('adminhtml')
			->__('Portofolii'));

		return $this;
	}

	public function indexAction()
	{
		$this->_initAction()->renderLayout();
	}

	public function editAction()
	{
		$id = $this -> getRequest() -> getParam('id');
		$model = Mage::getModel('portofoliu/albums') -> load($id);

		if ($model -> getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session') -> getFormData(true);
			if (!empty($data)) {
				$model -> setData($data);
			}

			Mage::register('portofoliu_data', $model);

			$this -> loadLayout();
			$this -> _setActiveMenu('portofoliu/items');

			$this -> _addBreadcrumb(Mage::helper('adminhtml') -> __('Item Manager'), Mage::helper('adminhtml') -> __('Item Manager'));
			$this -> _addBreadcrumb(Mage::helper('adminhtml') -> __('Item News'), Mage::helper('adminhtml') -> __('Item News'));

			$this -> getLayout() -> getBlock('head') -> setCanLoadExtJs(true);

			//$this -> _addContent($this -> getLayout() -> createBlock('portofoliu/adminhtml_portofoliu_edit')) -> _addLeft($this -> getLayout() -> createBlock('portofoliu/adminhtml_portofoliu_edit_tabs'));

			$this -> renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session') -> addError(Mage::helper('portofoliu') -> __('Item does not exist'));
			$this -> _redirect('*/*/');
		}
	}

	// public function newAction()
	// {
		// $this -> _forward('edit');
	// }

	// public function saveAction()
	// {
		// if ($data = $this -> getRequest() -> getPost()) {
// 
			// if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				// try {
					// /* Starting upload */
					// $uploader = new Varien_File_Uploader('filename');
// 
					// // Any extention would work
					// $uploader -> setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
					// $uploader -> setAllowRenameFiles(false);
// 
					// // Set the file upload mode
					// // false -> get the file directly in the specified folder
					// // true -> get the file in the product like folders
					// // (file.jpg will go in something like /media/f/i/file.jpg)
					// $uploader -> setFilesDispersion(false);
// 
					// // We set media as the upload dir
					// $path = Mage::getBaseDir('media') . DS;
					// $uploader -> save($path, $_FILES['filename']['name']);
// 
				// } catch (Exception $e) {
// 
				// }
// 
				// //this way the name is saved in DB
				// $data['filename'] = $_FILES['filename']['name'];
			// }
// 
			// $model = Mage::getModel('portofoliu/portofoliu');
			// $model -> setData($data) -> setId($this -> getRequest() -> getParam('id'));
// 
			// try {
				// if ($model -> getCreatedTime == NULL || $model -> getUpdateTime() == NULL) {
					// $model -> setCreatedTime(now()) -> setUpdateTime(now());
				// } else {
					// $model -> setUpdateTime(now());
				// }
// 
				// $model -> save();
				// Mage::getSingleton('adminhtml/session') -> addSuccess(Mage::helper('portofoliu') -> __('Item was successfully saved'));
				// Mage::getSingleton('adminhtml/session') -> setFormData(false);
// 
				// if ($this -> getRequest() -> getParam('back')) {
					// $this -> _redirect('*/*/edit', array('id' => $model -> getId()));
					// return;
				// }
				// $this -> _redirect('*/*/');
				// return;
			// } catch (Exception $e) {
				// Mage::getSingleton('adminhtml/session') -> addError($e -> getMessage());
				// Mage::getSingleton('adminhtml/session') -> setFormData($data);
				// $this -> _redirect('*/*/edit', array('id' => $this -> getRequest() -> getParam('id')));
				// return;
			// }
		// }
		// Mage::getSingleton('adminhtml/session') -> addError(Mage::helper('portofoliu') -> __('Unable to find item to save'));
		// $this -> _redirect('*/*/');
	// }

	public function deleteAction()
	{
		if ($this -> getRequest() -> getParam('id') > 0) {
			try {
				$model = Mage::getModel('portofoliu/portofoliu');

				$model -> setId($this -> getRequest() -> getParam('id')) -> delete();

				Mage::getSingleton('adminhtml/session') -> addSuccess(Mage::helper('adminhtml') -> __('Item was successfully deleted'));
				$this -> _redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session') -> addError($e -> getMessage());
				$this -> _redirect('*/*/edit', array('id' => $this -> getRequest() -> getParam('id')));
			}
		}
		$this -> _redirect('*/*/');
	}

	public function massDeleteAction()
	{
		$portofoliuIds = $this -> getRequest() -> getParam('portofoliu');
		if (!is_array($portofoliuIds)) {
			Mage::getSingleton('adminhtml/session') -> addError(Mage::helper('adminhtml') -> __('Please select item(s)'));
		} else {
			try {
				foreach ($portofoliuIds as $portofoliuId) {
					$portofoliu = Mage::getModel('portofoliu/portofoliu') -> load($portofoliuId);
					$portofoliu -> delete();
				}
				Mage::getSingleton('adminhtml/session') -> addSuccess(Mage::helper('adminhtml') -> __('Total of %d record(s) were successfully deleted', count($portofoliuIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session') -> addError($e -> getMessage());
			}
		}
		$this -> _redirect('*/*/index');
	}

	// public function massStatusAction()
	// {
		// $portofoliuIds = $this -> getRequest() -> getParam('portofoliu');
		// if (!is_array($portofoliuIds)) {
			// Mage::getSingleton('adminhtml/session') -> addError($this -> __('Please select item(s)'));
		// } else {
			// try {
				// foreach ($portofoliuIds as $portofoliuId) {
					// $portofoliu = Mage::getSingleton('portofoliu/portofoliu') -> load($portofoliuId) -> setStatus($this -> getRequest() -> getParam('status')) -> setIsMassupdate(true) -> save();
				// }
				// $this -> _getSession() -> addSuccess($this -> __('Total of %d record(s) were successfully updated', count($portofoliuIds)));
			// } catch (Exception $e) {
				// $this -> _getSession() -> addError($e -> getMessage());
			// }
		// }
		// $this -> _redirect('*/*/index');
	// }

}
