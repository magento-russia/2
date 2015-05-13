<?php
class Df_YandexMarket_CategoryController extends Mage_Core_Controller_Front_Action {
	/** @return void */
	public function suggestAction() {
		Df_YandexMarket_Model_Action_Category_Suggest::i($this)->process();
	}
}