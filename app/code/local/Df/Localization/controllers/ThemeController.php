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
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/** @return void */
	public function processAction() {Df_Localization_Model_Onetime_Action::i($this)->process();}

	/** @return bool */
	protected function _isAllowed() {return df_enabled(Df_Core_Feature::LOCALIZATION);}
}