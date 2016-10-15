<?php
/** @method Df_Core_Controller_Admin _title($text = null, $resetIfExists = true) */
abstract class Df_Core_Controller_Admin extends Mage_Adminhtml_Controller_Action {
	/**
	 * @param string $label
	 * @param string $title
	 * @param string|null $link [optional]
	 * @return Df_Core_Controller_Admin
	 */
	public function addBreadcrumb($label, $title, $link = null) {
		$this->_addBreadcrumb($label, $title, $link);
		return $this;
	}

	/**
	 * @param string $path
	 * @param array $arguments
	 * @return void
	 */
	public function redirect($path, $arguments = array()) {$this->_redirect($path, $arguments);}

	/**
	 * @param string $menuPath
	 * @return Df_Core_Controller_Admin
	 */
	public function setActiveMenu($menuPath) {
		$this->_setActiveMenu($menuPath);
		return $this;
	}

	/**
	 * @param string|bool|-1|null $text
  	 * @param bool $resetIfExists
	 * @return Df_Core_Controller_Admin
	 */
	public function title($text = null, $resetIfExists = true) {
		$this->_title($text, $resetIfExists);
		return $this;
	}

	/**
	 * @override
	 */
	protected function _renderTitles() {
		$this->_removeDefaultTitle = true;
		parent::_renderTitles();
	}
}