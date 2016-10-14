<?php
class Df_Localization_ThemeController extends Df_Core_Controller_Admin {
	/** @return void */
	public function indexAction() {
		try {
			$this
				->_title('Система')
				->_title('Локализация')
				->_title('Русификация оформительской темы')
				->loadLayout()
				->_setActiveMenu('system/df_localization/theme')
				->renderLayout()
			;
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/** @return void */
	public function processAction() {df_action($this, 'Df_Localization_Onetime_Action');}
}