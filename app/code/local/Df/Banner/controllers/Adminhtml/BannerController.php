<?php
class Df_Banner_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action {
	/** @return void */
	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0 ) {
			try {
				/** @var Df_Banner_Model_Banner $model */
				$model = Df_Banner_Model_Banner::i();
				$model->setId($this->getRequest()->getParam('id'));
				$model->delete();
				rm_session()->addSuccess(df_mage()->adminhtml()->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				rm_exception_to_session($e);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	/** @return void */
	public function editAction() {
		$id	= $this->getRequest()->getParam('id');
		/** @var Df_Banner_Model_Banner $model */
		$model = Df_Banner_Model_Banner::i();
		$model->load($id);
		if ($model->getId() || (0 === rm_nat0($id))) {
			$data = rm_session()->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			Mage::register('df_banner_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('df_banner/banners');
			$this->_addBreadcrumb(df_mage()->adminhtml()->__('Рекламные щиты'), df_mage()->adminhtml()->__('Рекламные щиты'));
			$this->_addBreadcrumb(df_mage()->adminhtml()->__('Banner News'), df_mage()->adminhtml()->__('Banner News'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this
				->_addContent(rm_block_l(new Df_Banner_Block_Adminhtml_Banner_Edit))
				->_addLeft(rm_block_l(new Df_Banner_Block_Adminhtml_Banner_Edit_Tabs))
			;
			$this->renderLayout();
		} else {
			rm_session()->addError(df_h()->banner()->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	/** @return void */
	public function exportCsvAction() {
		$this->_sendUploadResponse('df_banner.csv', Df_Banner_Block_Adminhtml_Banner_Grid::csv());
	}

	/** @return void */
	public function exportXmlAction() {
		$this->_sendUploadResponse('df_banner.xml', Df_Banner_Block_Adminhtml_Banner_Grid::xml());
	}

	/** @return void */
	public function indexAction() {$this->_initAction()->renderLayout();}

	/** @return void */
	public function massDeleteAction() {
		/** @var int[] $dfBannerIds */
		$dfBannerIds = $this->getRequest()->getParam('df_banner');
		if (!is_array($dfBannerIds)) {
			rm_session()->addError(df_mage()->adminhtml()->__('Please select banner(s)'));
		}
		else {
			try {
				foreach ($dfBannerIds as $dfBannerId) {
					/** @var int $dfBannerId */
					/** @var Df_Banner_Model_Banner $dfBanner */
					$dfBanner = Df_Banner_Model_Banner::ld($dfBannerId);
					$dfBanner->delete();
				}
				rm_session()->addSuccess(df_mage()->adminhtml()->__(
					'Total of %d record(s) were successfully deleted', count($dfBannerIds)
				));
			}
			catch (Exception $e) {
				rm_exception_to_session($e);
			}
		}
		$this->_redirect('*/*/index');
	}

	/** @return void */
	public function massStatusAction()
	{
		$dfBannerIds = $this->getRequest()->getParam('df_banner');
		if (!is_array($dfBannerIds)) {
			rm_session()->addError($this->__('Please select banner(s)'));
		}
		else {
			try {
				foreach ($dfBannerIds as $dfBannerId) {
					Df_Banner_Model_Banner::ld($dfBannerId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save()
					;
				}
				rm_session()->addSuccess($this->__(
					'Total of %d record(s) were successfully updated', count($dfBannerIds)
				));
			}
			catch (Exception $e) {
				rm_exception_to_session($e);
			}
		}
		$this->_redirect('*/*/index');
	}

	/** @return void */
	public function newAction() {$this->_forward('edit');}

	/** @return void */
	public function saveAction() {
		/** @var array(string => mixed)|null $data */
		$data = $this->getRequest()->getPost();
		if ($data) {
			if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					// Any extention would work
			   		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
				} catch (Exception $e) {
				}

				//this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
			/** @var Df_Banner_Model_Banner $model */
			$model = Df_Banner_Model_Banner::i($data);
			$model->setId($this->getRequest()->getParam('id'));
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
				rm_session()->addSuccess('Рекламный щит утверждён');
				rm_session()->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				rm_exception_to_session($e);
				rm_session()->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		rm_session()->addError(df_h()->banner()->__('Unable to find banner to save'));
		$this->_redirect('*/*/');
	}

	/** @return Df_Banner_Adminhtml_BannerController */
	private function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('df_banner/banners')
			->_addBreadcrumb('Рекламные щиты', 'Рекламные щиты');
		return $this;
	}

	/**
	 * @param string $fileName
	 * @param string $content
	 * @param string $contentType
	 * @return void
	 */
	private function _sendUploadResponse(
		$fileName, $content, $contentType = 'application/octet-stream'
	) {
		$response = $this->getResponse();
		$response->setHeader('HTTP/1.1 200 OK','');
		$response->setHeader('Pragma', 'public', true);
		$response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
		$response->setHeader('Last-Modified', date('r'));
		$response->setHeader('Accept-Ranges', 'bytes');
		$response->setHeader('Content-Length', strlen($content));
		rm_response_content_type($response, $contentType);
		$response->setBody($content);
		$response->sendResponse();
		die;
	}
}