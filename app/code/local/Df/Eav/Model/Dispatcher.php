<?php
class Df_Eav_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function core_collection_abstract_load_after(Varien_Event_Observer $observer) {
		try {
			/**
			 * Для ускорения работы системы проверяем класс коллекции прямо здесь,
			 * а не в обработчике события.
			 * Это позволяет нам не создавать обработчики событий для каждой коллекции.
			 */
			$collection = $observer->getData('collection');
			if (
					rm_loc()->isEnabled()
				&&
					df_h()->eav()->check()->entityAttributeCollection($collection)
			) {
				foreach ($collection as $attribute) {
					/** @var Mage_Eav_Model_Entity_Attribute $attribute */
					Df_Eav_Model_Translator::s()->translateAttribute($attribute);
				}
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function eav_entity_attribute_load_after(Varien_Event_Observer $observer) {
		try {
			if (rm_loc()->isEnabled()) {
				Df_Eav_Model_Translator::s()->translateAttribute($observer->getData('attribute'));
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}