<?php
class Df_Directory_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_collection_abstract_load_before(Varien_Event_Observer $observer) {
		/**
		 * Некоторые самописные скрипты приводили к сбою:
		 * «Call to undefined function df_h»,
		 * потому что они не вызывают Mage::dispatchEvent('default');
		 * @link http://magento-forum.ru/topic/3929/
		 */
		Df_Core_Boot::run();
		/**
		 * Для ускорения работы системы проверяем класс коллекции прямо здесь,
		 * а не в обработчике события.
		 * Это позволяет нам не создавать обработчики событий для каждой коллекции.
		 */
		$collection = $observer->getData('collection');
		/**
		 * Для ускорения работы системы проверяем класс коллекции прямо здесь,
		 * а не в обработчике события.
		 * Это позволяет нам не создавать обработчики событий для каждой коллекции.
		 */		
		if (df_h()->directory()->check()->regionCollection($collection)) {
			if (df_enabled(Df_Core_Feature::DIRECTORY)) {
				try {
					df_handle_event(
						Df_Directory_Model_Handler_OrderRegions::_CLASS
						,Df_Core_Model_Event_Core_Collection_Abstract_LoadBefore::_CLASS
						,$observer
					);
				}
				catch(Exception $e) {
					df_handle_entry_point_exception($e);
				}
			}
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_collection_abstract_load_after(Varien_Event_Observer $observer) {
		/**
		 * Для ускорения работы системы проверяем класс коллекции прямо здесь,
		 * а не в обработчике события.
		 * Это позволяет нам не создавать обработчики событий для каждой коллекции.
		 */
		$collection = $observer->getData('collection');
		if (
				df_h()->directory()->check()->regionCollection($collection)
			&&
				(
						df_cfg()->directory()->regionsRu()->getEnabled()
					||
						df_cfg()->directory()->regionsUa()->getEnabled()
				)
			&&
				df_enabled(Df_Core_Feature::DIRECTORY)
		) {
			try {
				df_handle_event(
					Df_Directory_Model_Handler_ProcessRegionsAfterLoading::_CLASS
					,Df_Core_Model_Event_Core_Collection_Abstract_LoadAfter::_CLASS
					,$observer
				);
			}

			catch(Exception $e) {
				df_handle_entry_point_exception($e);
			}
		}
	}
}