<?php
class Df_YandexMarket_YmlController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function indexAction() {
		Df_YandexMarket_Model_Action_Front::i($this)->process();
	}
}