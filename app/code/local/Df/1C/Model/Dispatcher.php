<?php
class Df_1C_Model_Dispatcher {
	/**
	 * @param Varien_Event_Observer $observer
	 * @return void
	 */
	public function df_catalog__attribute_set__group_added(
		Varien_Event_Observer $observer
	) {
		try {
			if (df_h()->_1c()->cml2()->isItCml2Processing()) {
				/** @var Df_Catalog_Model_Event_AttributeSet_GroupAdded $event */
				$event = Df_Catalog_Model_Event_AttributeSet_GroupAdded::i($observer);
				rm_1c_log(
					'Добавили к прикладному типу товаров «%s» группу свойств «%s»'
					,$event->getAttributeSet()->getDataUsingMethod(
						Df_Eav_Model_Entity_Attribute_Set::P__NAME
					)
					,$event->getGroupName()
				);
			}
		}
		catch(Exception $e) {
			df_handle_entry_point_exception($e);
		}
	}
}