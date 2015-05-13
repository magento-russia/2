<?php
class Df_Logging_Adminhtml_LoggingController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Archive page
	 * @return void
	 */
	public function archiveAction() {
		$this
			->_title($this->__('System'))
			->_title($this->__('Admin Actions Logs'))
			->_title($this->__('Archive'))
		;
		$this->loadLayout();
		$this->_setActiveMenu('system/df_logging');
		$this->renderLayout();
	}

	/** @return void */
	public function archiveGridAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/** @return void */
	public function detailsAction() {
		$this
			->_title($this->__('System'))
			->_title($this->__('Admin Actions Logs'))
			->_title($this->__('Report'))
			->_title($this->__('View Entry'))
		;
		$eventId = $this->getRequest()->getParam('event_id');
		$model   = df_model('df_logging/event')
			->load($eventId);
		if (!$model->getId()) {
			$this->_redirect('*/*/');
			return;
		}
		Mage::register('current_event', $model);
		$this->loadLayout();
		$this->_setActiveMenu('system/df_logging');
		$this->renderLayout();
	}

	/**
	 * Download archive file
	 * @return void
	 */
	public function downloadAction() {
		/** @var Df_Logging_Model_Archive $archive */
		$archive = Df_Logging_Model_Archive::i();
		$archive->loadByBaseName($this->getRequest()->getParam('basename'));
		if ($archive->getFilename()) {
			$this
				->_prepareDownloadResponse(
					$archive->getBaseName()
					,$archive->getContents()
					,$archive->getMimeType()
				)
			;
		}
	}

	/**
	 * Export log to CSV
	 * @return void
	 */
	public function exportCsvAction() {
		$this->_prepareDownloadResponse(
			'log.csv', Df_Logging_Block_Adminhtml_Index_Grid::i()->getCsvFile()
		);
	}

	/** @return void */
	public function exportXmlAction() {
		$this->_prepareDownloadResponse(
			'log.xml', Df_Logging_Block_Adminhtml_Index_Grid::i()->getExcelFile()
		);
	}

	/** @return void */
	public function gridAction() {
		$this->loadLayout();
		$this->renderLayout();
	}

	/** @return void */
	public function indexAction() {
		$this
			->_title($this->__('System'))
			->_title($this->__('Admin Actions Logs'))
			->_title($this->__('Report'))
		;
		$this->loadLayout();
		$this->_setActiveMenu('system/df_logging');
		$this->renderLayout();
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function _isAllowed() {
		/** @var bool $result */
		$result = false;
		if (df_enabled(Df_Core_Feature::LOGGING)) {
			switch($this->getRequest()->getActionName()) {
				case 'archive':
				case 'download':
				case 'archiveGrid':
					$result = df_mage()->admin()->session()->isAllowed('admin/system/df_logging/backups');
					break;
				case 'grid':
				case 'exportCsv':
				case 'exportXml':
				case 'details':
				case 'index':
					$result = df_mage()->admin()->session()->isAllowed('admin/system/df_logging/events');
					break;
			}
		}
		return $result;
	}
}