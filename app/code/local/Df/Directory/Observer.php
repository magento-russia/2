<?php
class Df_Directory_Observer {
	/**
	 * @see Mage_Core_Model_Resource_Db_Collection_Abstract::_afterLoad():
			Mage::dispatchEvent('core_collection_abstract_load_after', array('collection' => $this));
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_collection_abstract_load_after(Varien_Event_Observer $o) {
		// Для ускорения работы системы проверяем класс коллекции прямо здесь,
		// а не в обработчике события.
		// Это позволяет нам не создавать обработчики событий для каждой коллекции.
		/** @var Mage_Core_Model_Resource_Db_Collection_Abstract $collection */
		$collection = $o['collection'];
		/**
		 * 2015-03-24
		 * Проверку @uses isRegionCollection() надо обязательно выполнить до вызова @uses df_cfg(),
		 * потому что если событие «core_collection_abstract_load_after»
		 * относится к коллекции магазинов @see Mage_Core_Model_App::_initStores(),
		 * то использовать @uses df_cfg() ещё нельзя: текущий магазие ещё не инициализирован.
		 */
		if (self::isRegionCollection($collection)) {
			/** @var Df_Directory_Settings $cfg */
			static $cfg; if (!$cfg) {$cfg = df_cfg()->directory();};
			if (
					(
						$cfg->regionsRu()->getEnabled()
						 || $cfg->regionsUa()->getEnabled()
						 || $cfg->regionsKz()->getEnabled()
					)
			) {
				try {
					df_handle_event(
						Df_Directory_Model_Handler_ProcessRegionsAfterLoading::class
						,Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter::class
						,$o
					);
				}
				catch (Exception $e) {
					df_handle_entry_point_exception($e);
				}
			}
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_collection_abstract_load_before(Varien_Event_Observer $o) {
		/**
		 * Некоторые самописные скрипты приводили к сбою:
		 * «Call to undefined function df_h»,
		 * потому что они не вызывают Mage::dispatchEvent('default');
		 * http://magento-forum.ru/topic/3929/
		 */
		Df_Core_Boot::run();
		/**
		 * Для ускорения работы системы проверяем класс коллекции прямо здесь,
		 * а не в обработчике события.
		 * Это позволяет нам не создавать обработчики событий для каждой коллекции.
		 */
		$collection = $o['collection'];
		if (self::isRegionCollection($collection)) {
			try {
				df_handle_event(
					Df_Directory_Model_Handler_OrderRegions::class
					,Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore::class
					,$o
				);
			}
			catch (Exception $e) {
				df_handle_entry_point_exception($e);
			}
		}
	}

	/**
	 * @used-by core_collection_abstract_load_after()
	 * @used-by core_collection_abstract_load_before()
	 * @var Varien_Data_Collection_Db $collection
	 * @return bool
	 */
	private static function isRegionCollection(Varien_Data_Collection_Db $collection) {
		return df_is($collection,
			'Mage_Directory_Model_Resource_Region_Collection'
			,'Df_Directory_Model_Resource_Region_Collection'
		);
	}
}