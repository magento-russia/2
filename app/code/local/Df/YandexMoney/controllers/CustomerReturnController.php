<?php
class Df_YandexMoney_CustomerReturnController extends Mage_Core_Controller_Front_Action {
	/**
	 * Яндекс.Деньги передают сюда результат авторизации приложения
	 * @return void
	 */
	public function indexAction() {df_action($this);}
}