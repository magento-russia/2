<?php
class Df_Banner_Adminhtml_BanneritemController extends Mage_Adminhtml_Controller_Action {
	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0 ) {
			try {
				/** @var Df_Banner_Model_Banneritem $model */
				$model = Df_Banner_Model_Banneritem::i();
				$model->setId($this->getRequest()->getParam('id'));
				$model->delete();
				df_session()->addSuccess(df_mage()->adminhtml()->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				df_exception_to_session($e);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		/** @var Df_Banner_Model_Banneritem $model */
		$model = Df_Banner_Model_Banneritem::i();
		$model->load($id);
		if ($model->getId() || (0 === df_nat0($id))) {
			$data = df_session()->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('df_banner_item_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('df_banner/banneritems');
			$this->_addBreadcrumb(df_mage()->adminhtml()->__('Рекламные объявления'), df_mage()->adminhtml()->__('Рекламные объявления'));
			$this->_addBreadcrumb(df_mage()->adminhtml()->__('Banner Item News'), df_mage()->adminhtml()->__('Banner Item News'));
			/** @noinspection PhpUndefinedMethodInspection */
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this
				->_addContent(df_block_l(new Df_Banner_Block_Adminhtml_Banneritem_Edit))
				->_addLeft(df_block_l(new Df_Banner_Block_Adminhtml_Banneritem_Edit_Tabs))
			;
			$this->renderLayout();
		} else {
			df_session()->addError(df_h()->banner()->__('Banner Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	/** @return void */
	public function exportCsvAction() {
		$this->_sendUploadResponse('df_banner.csv', Df_Banner_Block_Adminhtml_Banneritem_Grid::csv());
	}

	/** @return void */
	public function exportXmlAction() {
		$this->_sendUploadResponse('df_banner.xml', Df_Banner_Block_Adminhtml_Banneritem_Grid::xml());
	}

	public function indexAction() {
		$this->_initAction();
		$this->renderLayout();
	}

	public function massDeleteAction() {
		/** @var int[] $dfBannerIds */
		$dfBannerIds = $this->getRequest()->getParam('df_banner_item');
		if (!is_array($dfBannerIds)) {
			df_session()->addError(df_mage()->adminhtml()->__('Please select banner item(s)'));
		}
		else {
			try {
				foreach ($dfBannerIds as $dfBannerId) {
					/** @var int $dfBannerId */
					/** @var Df_Banner_Model_Banneritem $dfBanner */
					$dfBanner = Df_Banner_Model_Banneritem::ld($dfBannerId);
					$dfBanner->delete();
				}
				df_session()->addSuccess(df_mage()->adminhtml()->__(
					'Total of %d record(s) were successfully deleted', count($dfBannerIds)
				));
			}
			catch (Exception $e) {
				df_exception_to_session($e);
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massStatusAction()
	{
		$dfBannerIds = $this->getRequest()->getParam('df_banner_item');
		if (!is_array($dfBannerIds)) {
			df_session()->addError($this->__('Please select banner item(s)'));
		}
		else {
			try {
				foreach ($dfBannerIds as $dfBannerId) {
					Df_Banner_Model_Banneritem::ld($dfBannerId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save()
					;
				}
				df_session()->addSuccess($this->__(
					'Total of %d record(s) were successfully updated', count($dfBannerIds)
				));
			}
			catch (Exception $e) {
				df_exception_to_session($e);
			}
		}
		$this->_redirect('*/*/index');
	}

	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		$data = $this->getRequest()->getPost();
		if ($data) {
			if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('image');
					// Any extention would work
			   		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'df_banner' . DS;
					$result = $uploader->save($path, $_FILES['image']['name'] );
					//this way the name is saved in DB
					$data['image'] = 'df_banner/'. $result['file'];
				} catch (Exception $e) {
				}
			} else {
				if (dfa(dfa($data, 'image', array()),'delete')) {
					 $data['image'] = '';
				} else {
					unset($data['image']);
				}
			}

			if (isset($data['banner_order'])){ $data['banner_order']= df_int($data['banner_order']); }

			if (isset($_FILES['thumb_image']['name']) && $_FILES['thumb_image']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('thumb_image');
					// Any extention would work
			   		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS . 'df_banner' . DS;
					$result = $uploader->save($path, $_FILES['thumb_image']['name'] );
					//this way the name is saved in DB
					$data['thumb_image'] = 'df_banner/'. $result['file'];
				} catch (Exception $e) {
				}

			} else {
				if (dfa(dfa($data, 'thumb_image', array()), 'delete')) {
					 $data['thumb_image'] = '';
				} else {
					unset($data['thumb_image']);
				}
			}
			/** @var Df_Banner_Model_Banneritem $model */
			$model = Df_Banner_Model_Banneritem::i($data);
			if ($this->getRequest()->getParam('id')){
				$model->setId($this->getRequest()->getParam('id'));
			}
			//exit;
			try {
				if (
						is_null($model->getCreatedTime())
					||
						is_null($model->getUpdateTime())
				) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
				$model->save();
				df_session()->addSuccess('Объявление сохранено.');
				df_session()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				df_exception_to_session($e);
				df_session()->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		df_session()->addError(df_h()->banner()->__('Unable to find banner item to save'));
		$this->_redirect('*/*/');
	}

	public function setOrderAction() {
		$params = $this->getRequest()->getParam('items');
		//var_dump($params);exit;
		if (!$params) {
			df_session()->addError(df_mage()->adminhtml()->__('Please select item(s)'));
		}
		else {
			try {
				$params = explode('|',$params);
				foreach ($params as $param) {
					$param = explode('-',$param);
					if (sizeof($param)>1){
						/** @var Df_Banner_Model_Banneritem $model */
						$model =
							Df_Banner_Model_Banneritem::i(
								array('banner_order'=>$param[1])
							)
						;
						$model->setId($param[0]);
						$model->save();
					}
				}
				df_session()->addSuccess(df_mage()->adminhtml()->__(
					'Total of %d record(s) were successfully deleted', count($params)
				));
			}
			catch (Exception $e) {
				df_exception_to_session($e);
			}
		}
		$this->_redirect('*/*/index');
	}

	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
	{
		$response = $this->getResponse();
		$response->setHeader('HTTP/1.1 200 OK','');
		$response->setHeader('Pragma', 'public', true);
		$response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
		$response->setHeader('Last-Modified', date('r'));
		$response->setHeader('Accept-Ranges', 'bytes');
		$response->setHeader('Content-Length', strlen($content));
		df_response_content_type($response, $contentType);
		$response->setBody($content);
		$response->sendResponse();
		die;
	}

	/** @return void */
	private function _initAction() {
		$this->loadLayout();
		$this->_setActiveMenu('df_banner/banneritems');
		$this->_addBreadcrumb('Рекламные объявления', 'Рекламные объявления');
	}
}