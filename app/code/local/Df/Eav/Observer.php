<?php
class Df_Eav_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function core_collection_abstract_load_after(Varien_Event_Observer $o) {
		try {
			// Для ускорения работы системы проверяем класс коллекции прямо здесь,
			// а не в обработчике события.
			// Это позволяет нам не создавать обработчики событий для каждой коллекции.
			$collection = $o['collection'];
			if (df_loc()->isEnabled() && self::isEntityAttributeCollection($collection)) {
				/** @uses Df_Eav_Model_Translator::translateAttribute() */
				df_map(array(Df_Eav_Model_Translator::s(), 'translateAttribute'), $collection);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function eav_entity_attribute_load_after(Varien_Event_Observer $o) {
		try {
			if (df_loc()->isEnabled()) {
				Df_Eav_Model_Translator::s()->translateAttribute($o['attribute']);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}

	/**
	 * @var Varien_Data_Collection_Db $c
	 * @return bool
	 */
	private static function isEntityAttributeCollection(Varien_Data_Collection_Db $c) {return
		$c instanceof Mage_Eav_Model_Resource_Entity_Attribute_Collection
	;}
}