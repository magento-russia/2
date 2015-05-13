<?php
/**
 * 2015-04-29
 * Обработка запросов от Яндекс.Кассы
 */
class Df_YandexMoney_KassaController extends Mage_Core_Controller_Front_Action {
	/**
	 * @link https://money.yandex.ru/my/tech-integration
	 * @return void
	 */
	public function checkAction() {
		rm_response_content_type($this->getResponse(), 'text/plain; charset=utf-8');
		$this->getResponse()->setBody(
			'На этот адрес мой сервер будет получать запросы'
			. ' на проверку параметров заказа перед оплатой.'
		);
	}

	/**
	 * @link https://money.yandex.ru/my/tech-integration
	 * @return void
	 */
	public function confirmAction() {
		rm_response_content_type($this->getResponse(), 'text/plain; charset=utf-8');
		$this->getResponse()->setBody(
			'На этот адрес мой сервер будет получать уведомления об успешных платежах.'
		);
	}
}