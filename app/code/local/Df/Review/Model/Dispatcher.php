<?php
class Df_Review_Model_Dispatcher {
	/**
	 * Обрабатываем данное событие
	 * для оповещения администратора о новых отзывах в магазине
	 * Обратите внимание, что это событие появилось только в Magento CE 1.4.2.0.
	 * Учитывая, что данная функциональность добавляется 2014-09-25
	 * и не является критически важной,
	 * мы сознательно игнорируем поддержку версий
	 * Magento CE 1.4 ниже версии 1.4.2.0,
	 * в то время как подавлябщее большинство
	 * остальных функций Российской сборки Magento
	 * совместимо с версиями Magento CE 1.4 ниже версии 1.4.2.0
	 * @see Mage_Core_Model_Session_Abstract::addMessage()
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_session_abstract_add_message(Varien_Event_Observer $observer) {
		try {
			/** @see Mage_Review_ProductController::postAction() */
			/**
			 * Обратите внимание,
			 * что событие «core_session_abstract_add_message» вовсе не означает,
			 * что сообщение было добавлено именно в сессию @see rm_session_core().
			 * Оно могло быть добавлено совсем в другую сессию
			 * (например, при добавлении товара в корзину),
			 * и тогда rm_session_core()->getMessages()->getLastAddedMessage() вернёт null!
			 * @link http://magento-forum.ru/topic/4688/
			 */
			/** @var Mage_Core_Model_Message_Abstract|null $lastAddedMessage */
			$lastAddedMessage = rm_session_core()->getMessages()->getLastAddedMessage();
			/**
			 * На всякий случай, перестраховываемся, и ещё проверяем,
			 * что текущая операция — «review_product_post».
			 */
			if (
					rm_state()->getController()
				&&
					('review_product_post' === rm_state()->getController()->getFullActionName())
				&&
					$lastAddedMessage
				&&
					in_array($lastAddedMessage->getCode(), $this->getReviewMessages())
			) {
				Df_Review_Model_Notifier::i()->process();
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}


	/** @return string[] */
	private function getReviewMessages() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_map(
					array(df_mage()->reviewHelper(), '__')
					,array(
						'Your review has been accepted for moderation.'
						// В Magento CE 1.4 в конце сообщения отсутствует точка.
						,'Your review has been accepted for moderation'
					)
				)
			;
		}
		return $this->{__METHOD__};
	}
}