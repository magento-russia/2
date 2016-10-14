<?php
class Df_Localization_VerificationController extends Df_Core_Controller_Admin {
	/** @return void */
	public function indexAction() {
		$this
			->_title($this->__('System'))
			->_title($this->__('Локализация'))
			->_title('Проверка полноты перевода')
			->loadLayout()
			->_setActiveMenu('system/df_localization')
			->renderLayout()
		;
	}
}