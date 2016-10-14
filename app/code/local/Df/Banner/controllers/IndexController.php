<?php
class Df_Banner_IndexController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
}