<?php
/**
 * Cообщение:		«admin_roles_save_after»
 * Источник:		Mage_Core_Model_Abstract::_afterSave()
 * [code]
		Mage::dispatchEvent('model_save_after', array('object'=>$this));
		Mage::dispatchEvent($this->_eventPrefix.'_save_after', $this->_getEventData());
 * [/code]
 *
 * Назначение:		Позволяет выполнить дополнительную обработку коллекции товарных разделов
 * 					перед её загрузкой
 */
class Df_Admin_Model_Event_Roles_Save_After extends Df_Core_Model_Event {
	/** @return Mage_Admin_Model_Roles */
	public function getRole() {return $this->getEventParam(self::EVENT_PARAM__ROLE);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__ROLE = 'object';
	const EXPECTED_EVENT_PREFIX = 'admin_roles_save_after';
}