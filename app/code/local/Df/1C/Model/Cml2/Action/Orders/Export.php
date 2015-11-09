<?php
// Экспорт заказов из интернет-магазина в 1С:Управление торговлей
class Df_1C_Model_Cml2_Action_Orders_Export extends Df_1C_Model_Cml2_Action_GenericExport {
	/**
	 * @override
	 * @return Df_1C_Model_Cml2_SimpleXml_Generator_Document_Orders
	 */
	protected function createDocument() {
		return Df_1C_Model_Cml2_SimpleXml_Generator_Document_Orders::_i2($this->getOrders());
	}

	/**
	 * Для тестирования
	 * @override
	 * @return Zend_Date
	 */
	protected function getLastProcessedTime() {
		/** @var Zend_Date $result */
		$result = parent::getLastProcessedTime();
		// для некоторых сценариев тестирования
		if (true && df_is_it_my_local_pc()) {
			$result = Zend_Date::now();
			/**
			 * Zend_Date::sub() возвращает число в виде строки для Magento CE 1.4.0.1
			 * и объект класса Zend_Date для более современных версий Magento
			 */
			$result->sub(7, Zend_Date::DAY);
		}
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needUpdateLastProcessedTime() {return true;}

	/** @return Df_Sales_Model_Resource_Order_Collection */
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
			$result->addFieldToFilter(Df_Sales_Model_Order::P__STORE_ID, rm_state()->getStoreProcessed()->getId());
			/**
			 * Магазин должен передавать в 1С: Управление торговлей 2 вида заказов:
			 * 1) Заказы, которые были созданы в магазине ПОСЛЕ последнего сеанса передачи данных
			 * 2) Заказы, которые были изменены в магазине ПОСЛЕ последнего сеанса передачи данных
			 * Как я понимаю, оба ограничения можно наложить единым фильтром:
			 * по времени изменения заказа.
			 */
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
			$result
				->addFieldToFilter(
					Df_Sales_Model_Order::P__UPDATED_AT
					,array(
						Df_Varien_Const::FROM => $this->getLastProcessedTime()
						,Df_Varien_Const::DATETIME => true
					)
				)
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_1C_Model_Cml2_Action_Orders_Export
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}