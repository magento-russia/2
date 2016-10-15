<?php
class Df_1C_Observer {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 * @param Varien_Event_Observer $o
	 * @return void
	 */
	public function df_catalog__attribute_set__group_added(Varien_Event_Observer $o) {
		try {
			if (df_is(rm_controller(), 'Df_1C_Cml2Controller')) {
				/** @var Df_Catalog_Model_Event_AttributeSet_GroupAdded $event */
				$event = Df_Catalog_Model_Event_AttributeSet_GroupAdded::i($o);
				rm_1c_log(
					'Добавили к прикладному типу товаров «%s» группу свойств «%s».'
					,$event->getAttributeSet()->getData(Df_Eav_Model_Entity_Attribute_Set::P__NAME)
					,$event->getGroupName()
				);
			}
		}
		catch (Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}