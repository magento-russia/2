<?php
require_once BP . '/app/code/core/Mage/Install/controllers/IndexController.php';
class Df_Install_IndexController extends Mage_Install_IndexController {
	/**
	 * @override
	 */
	public function indexAction() {
		$this->_forward('locale', 'wizard', 'install');
	}

}