<?php
class Df_Cms_Block_Admin_Page_Revision_Edit_Info extends Mage_Adminhtml_Block_Widget_Container {
	/** @return string */
	public function getAuthor() {
		$result = df_h()->cms()->__('N/A');
		/** @var int $userId */
		$userId = df_nat0($this->_page->getUserId());
		if ($userId === rm_admin_id()) {
			$result = rm_admin_name();
		}
		else {
			/** @var Df_Admin_Model_User $user */
			$user = Df_Admin_Model_User::ld($userId);
			if ($user->getId()) {
				$result = $user->getUsername();
			}
		}
		return $result;
	}

	/** @return string */
	public function getCreatedAt() {
		/** @var string $format */
		$format =
			Mage::app()->getLocale()->getDateTimeFormat(
				Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
			)
		;
		/** @var string $data */
		$result = $this->_page->getRevisionCreatedAt();
		try {
			$result =
				df_dts(
					Mage::app()->getLocale()->date($result, Varien_Date::DATETIME_INTERNAL_FORMAT)
					, $format
				)
			;
		} catch (Exception $e) {
			$result = df_h()->cms()->__('N/A');
		}
		return $result;
	}

	/** @return string */
	public function getRevisionId(){
		return
			$this->_page->getRevisionId()
			? $this->_page->getRevisionId()
			: df_h()->cms()->__('N/A')
		;
	}

	/** @return string */
	public function getRevisionNumber() {
		return $this->_page->getRevisionNumber();
	}

	/**
	 * Prepare version identifier. It should be
	 * label or id if first one not assigned.
	 * Also can be N/A.
	 * @return string
	 */
	public function getVersion() {
		/** @var string $result */
		$result = $this->_page->getLabel() ? $this->_page->getLabel() : $this->_page->getVersionId();
		if (!$result) {
			$result = df_h()->cms()->__('N/A');
		}
		return $result;
	}

	/** @return string */
	public function getVersionLabel() {
		return
			$this->_page->getLabel()
			? $this->_page->getLabel()
			: df_h()->cms()->__('N/A')
		;
	}

	/** @return string */
	public function getVersionNumber() {
		return
			$this->_page->getVersionNumber()
			? $this->_page->getVersionNumber()
			: df_h()->cms()->__('N/A')
		;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_page = Mage::registry('cms_page');
	}
	/**
	 * Currently loaded page model
	 * @var Df_Cms_Model_Page
	 */
	protected $_page;
}