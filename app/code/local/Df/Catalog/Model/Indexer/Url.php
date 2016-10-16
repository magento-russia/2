<?php
class Df_Catalog_Model_Indexer_Url extends Mage_Catalog_Model_Indexer_Url {
	/**
	 * Цель перекрытия —
	 * избежание преждевременной перестройки расчётных таблиц
	 * в процессе массовой замены адресных ключей товарных разделов.
	 * @override
	 * @param Mage_Index_Model_Event $event
	 */
	protected function _registerCategoryEvent(Mage_Index_Model_Event $event) {
		/** @noinspection PhpUndefinedMethodInspection */
		if (!$event->getDataObject()->getExcludeUrlRewrite()) {
			//
			// Мы ввели для товарного раздела дополнительный флаг «exclude_url_rewrite»
			// по аналогии с одноимённым флагом товара.
			//
			// Это позволяет нам избежать перестройки перенаправлений
			// в процессе массовой замены адресных ключей товарных разделов.
			//
			// А избегаем мы перестройки по той причине,
			// что что за массовой заменой адресных ключей товарных разделов
			// мы хотим сначала выполнить такую же массовую замену адресных ключей товаров,
			// и лишь затем перестроить перенаправления.
			//
			parent::_registerCategoryEvent($event);
		}
	}
}