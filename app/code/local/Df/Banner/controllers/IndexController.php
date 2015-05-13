<?php
class Df_Banner_IndexController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		if (df_enabled(Df_Core_Feature::BANNER)) {
			$this->loadLayout();
			$this->renderLayout();
		}
	}
}