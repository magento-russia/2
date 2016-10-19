<?php
// Экспорт заказов из интернет-магазина в 1С:Управление торговлей
class Df_1C_Cml2_Action_Orders_Export extends Df_1C_Cml2_Action_GenericExport {
	/**
	 * @override
	 * @see Df_1C_Cml2_Action_GenericExport::createDocument()
	 * @used-by Df_1C_Cml2_Action_GenericExport::getDocument()
	 * @return Df_1C_Cml2_Export_Document_Orders
	 */
	protected function createDocument() {
		return Df_1C_Cml2_Export_Document_Orders::i($this->getOrders());
	}

	/**
	 * Запоминаем время последней успешной обработки данных.
	 * Это время нам нужно, например, в сценариях обработки заказов,
	 * потому что магазин должен передавать в 1С:Управление торговлей 2 вида заказов,
	 * и для определения обоих видов используется время последнего сеанса передачи данных:
	 * 1) Заказы, которые были созданы в магазине ПОСЛЕ последнего сеанса передачи данных
	 * 2) Заказы, которые были изменены в магазине ПОСЛЕ последнего сеанса передачи данных
	 * @override
	 * @see Df_Core_Model_Action::processFinish()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function processFinish() {
		parent::processFinish();
		df_1c()->saveConfigValue(self::$LAST_PROCESSED_KEY, df_dts(Zend_Date::now()));
	}

	/**
	 * @used-by getOrders()
	 * @return Zend_Date
	 */
	private function getLastProcessedTime() {
		if (!isset($this->{__METHOD__})) {
			/** @var Zend_Date $result */
			// для некоторых сценариев тестирования
			if (true && df_my_local()) {
				$result = Zend_Date::now();
				/**
				 * Zend_Date::sub() возвращает число в виде строки для Magento CE 1.4.0.1
				 * и объект класса Zend_Date для более современных версий Magento
				 */
				$result->sub(7, Zend_Date::DAY);
			}
			else {
				$result = null;
				/** @var string $timeS */
				$timeS = df_nts($this->getStoreConfig(self::$LAST_PROCESSED_KEY));
				if ($timeS) {
					try {
						// Мы вправе рассчитывать на стандартный для Zend_Date формат даты,
						// потому что предварительно именно в этом формате дата была сохранена.
						$result = new Zend_Date($timeS);
					}
					catch (Exception $e) {}
				}
			}
			$this->{__METHOD__} = $result ?: df_date_least();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by createDocument()
	 * @return Df_Sales_Model_Resource_Order_Collection
	 */
	private function getOrders() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Sales_Model_Resource_Order_Collection $result */
			$result = Df_Sales_Model_Order::c();
			/** @var Zend_Db_Adapter_Abstract $adapter */
			$adapter = $result->getSelect()->getAdapter();
			// Отбраковываем из коллекции заказы других магазинов
			/**
			 * 2015-11-09
			 * Убрал вызов @see Zend_Db_Adapter_Abstract::quoteIdentifier()
			 * для совместимости с Magento CE 1.9.2.2,
			 * потому что эта версия по соображениям безопасности магазина
			 * после установки неряшливо написанных сторонних модулей
			 * сама добавляет кавычки ко всем полям, указанным в методе
			 * @uses Varien_Data_Collection_Db::addFieldToFilter(),
			 * и когда качественно написанный модуль добавляет свои кавычки,
			 * то получается, что ядро, в угоду неряшливо написанным модулям
			 * бездумно добавляет дополнительные кавычки,
			 * и в командах SQL имена полей получаются некорректными, например: AND (```is_active``` = 1)
			 * @see Varien_Data_Collection_Db::_translateCondition():
					$quotedField = $this->getConnection()->quoteIdentifier($field);
			 * https://github.com/OpenMage/magento-mirror/blob/92a1142a37a1f8f639db95353199368f5784725d/lib/Varien/Data/Collection/Db.php#L417
			 */
			$result->addFieldToFilter(Df_Sales_Model_Order::P__STORE_ID, $this->storeId());
			/**
			 * Магазин должен передавать в 1С:Управление торговлей 2 вида заказов:
			 * 1) Заказы, которые были созданы в магазине ПОСЛЕ последнего сеанса передачи данных
			 * 2) Заказы, которые были изменены в магазине ПОСЛЕ последнего сеанса передачи данных
			 * Как я понимаю, оба ограничения можно наложить единым фильтром:
			 * по времени изменения заказа.
			 */
			$result->addFieldToFilter(Df_Sales_Model_Order::P__UPDATED_AT, array(
				'from' => $this->getLastProcessedTime(), 'datetime' => true
			));
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @var string */
	private static $LAST_PROCESSED_KEY = 'df_1c/orders_export/last_processed_time';
}