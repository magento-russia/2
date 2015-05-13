<?php
class Df_YandexMoney_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * Яндекс.Деньги передают сюда результат авторизации приложения
	 * @return void
	 */
	public function indexAction() {Df_YandexMoney_Model_Action_CustomerReturn::i($this)->process();}
}